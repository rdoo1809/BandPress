#!/usr/bin/env node

import { Server } from '@modelcontextprotocol/sdk/server/index.js';
import { StdioServerTransport } from '@modelcontextprotocol/sdk/server/stdio.js';
import {
  CallToolRequestSchema,
  ErrorCode,
  ListToolsRequestSchema,
  McpError,
} from '@modelcontextprotocol/sdk/types.js';
import axios from 'axios';
import * as fs from 'fs';
import * as path from 'path';

// Load environment variables
import * as dotenv from 'dotenv';
dotenv.config({ path: path.join(process.cwd(), 'config.env') });

const FIGMA_ACCESS_TOKEN = process.env.FIGMA_ACCESS_TOKEN;
if (!FIGMA_ACCESS_TOKEN || FIGMA_ACCESS_TOKEN === 'YOUR_FIGMA_ACCESS_TOKEN_HERE') {
  console.error('FIGMA_ACCESS_TOKEN environment variable is required. Please set it in config.env');
  process.exit(1);
}

// Figma API client
const figmaApi = axios.create({
  baseURL: 'https://api.figma.com/v1',
  headers: {
    'X-Figma-Token': FIGMA_ACCESS_TOKEN,
  },
});

interface FigmaFile {
  document: any;
  components: Record<string, any>;
  componentSets: Record<string, any>;
  schemaVersion: number;
  styles: Record<string, any>;
  name: string;
  lastModified: string;
  thumbnailUrl: string;
  version: string;
  role: string;
}

interface DesignToken {
  colors: Record<string, string>;
  typography: Record<string, any>;
  spacing: Record<string, string>;
  borderRadius: Record<string, string>;
}

// MCP Server implementation
class FigmaMCPServer {
  private server: Server;

  constructor() {
    this.server = new Server(
      {
        name: 'figma-mcp-server',
        version: '1.0.0',
      },
      {
        capabilities: {
          tools: {},
        },
      }
    );

    this.setupToolHandlers();
  }

  private setupToolHandlers() {
    // List available tools
    this.server.setRequestHandler(ListToolsRequestSchema, async () => {
      return {
        tools: [
          {
            name: 'read_figma_file',
            description: 'Read and analyze a Figma file structure',
            inputSchema: {
              type: 'object',
              properties: {
                file_id: {
                  type: 'string',
                  description: 'Figma file ID (from URL or share link)',
                },
                node_id: {
                  type: 'string',
                  description: 'Optional: Specific node ID to focus on',
                },
              },
              required: ['file_id'],
            },
          },
          {
            name: 'extract_design_tokens',
            description: 'Extract design system tokens from a Figma file',
            inputSchema: {
              type: 'object',
              properties: {
                file_id: {
                  type: 'string',
                  description: 'Figma file ID',
                },
              },
              required: ['file_id'],
            },
          },
          {
            name: 'analyze_components',
            description: 'Analyze reusable components in a Figma file',
            inputSchema: {
              type: 'object',
              properties: {
                file_id: {
                  type: 'string',
                  description: 'Figma file ID',
                },
              },
              required: ['file_id'],
            },
          },
          {
            name: 'generate_vue_component',
            description: 'Generate Vue component code from Figma component',
            inputSchema: {
              type: 'object',
              properties: {
                file_id: {
                  type: 'string',
                  description: 'Figma file ID',
                },
                component_id: {
                  type: 'string',
                  description: 'Component ID to generate code for',
                },
                component_name: {
                  type: 'string',
                  description: 'Name for the generated Vue component',
                },
              },
              required: ['file_id', 'component_id', 'component_name'],
            },
          },
          {
            name: 'export_figma_assets',
            description: 'Export images and assets from Figma file',
            inputSchema: {
              type: 'object',
              properties: {
                file_id: {
                  type: 'string',
                  description: 'Figma file ID',
                },
                format: {
                  type: 'string',
                  enum: ['PNG', 'JPG', 'SVG', 'PDF'],
                  description: 'Export format',
                  default: 'PNG',
                },
                scale: {
                  type: 'number',
                  description: 'Export scale (1-4)',
                  default: 1,
                  minimum: 1,
                  maximum: 4,
                },
              },
              required: ['file_id'],
            },
          },
        ],
      };
    });

    // Handle tool calls
    this.server.setRequestHandler(CallToolRequestSchema, async (request) => {
      const { name, arguments: args } = request.params;

      try {
        switch (name) {
          case 'read_figma_file':
            return await this.handleReadFigmaFile(args);
          case 'extract_design_tokens':
            return await this.handleExtractDesignTokens(args);
          case 'analyze_components':
            return await this.handleAnalyzeComponents(args);
          case 'generate_vue_component':
            return await this.handleGenerateVueComponent(args);
          case 'export_figma_assets':
            return await this.handleExportAssets(args);
          default:
            throw new McpError(
              ErrorCode.MethodNotFound,
              `Unknown tool: ${name}`
            );
        }
      } catch (error) {
        throw new McpError(
          ErrorCode.InternalError,
          `Tool execution failed: ${error instanceof Error ? error.message : String(error)}`
        );
      }
    });
  }

  private async handleReadFigmaFile(args: any) {
    const { file_id, node_id } = args;

    try {
      const response = await figmaApi.get(`/files/${file_id}`);
      const file: FigmaFile = response.data;

      let content = `## Figma File: ${file.name}\n\n`;
      content += `**Last Modified:** ${file.lastModified}\n`;
      content += `**Version:** ${file.version}\n\n`;

      if (node_id) {
        // Focus on specific node
        const node = this.findNodeById(file.document, node_id);
        if (node) {
          content += `### Node: ${node.name}\n\n`;
          content += `\`\`\`json\n${JSON.stringify(node, null, 2)}\n\`\`\`\n`;
        } else {
          content += `Node ${node_id} not found in file.\n`;
        }
      } else {
        // Show file overview
        content += `### Components (${Object.keys(file.components).length})\n`;
        Object.entries(file.components).forEach(([id, component]) => {
          content += `- ${component.name} (${id})\n`;
        });

        content += `\n### Styles (${Object.keys(file.styles).length})\n`;
        Object.entries(file.styles).forEach(([id, style]) => {
          content += `- ${style.name} (${style.styleType})\n`;
        });
      }

      return {
        content: [
          {
            type: 'text',
            text: content,
          },
        ],
      };
    } catch (error) {
      throw new Error(`Failed to read Figma file: ${error instanceof Error ? error.message : String(error)}`);
    }
  }

  private async handleExtractDesignTokens(args: any) {
    const { file_id } = args;

    try {
      const response = await figmaApi.get(`/files/${file_id}`);
      const file: FigmaFile = response.data;

      const tokens: DesignToken = {
        colors: {},
        typography: {},
        spacing: {},
        borderRadius: {},
      };

      // Extract styles
      Object.entries(file.styles).forEach(([id, style]) => {
        if (style.styleType === 'FILL') {
          tokens.colors[style.name] = this.extractColorValue(style);
        } else if (style.styleType === 'TEXT') {
          tokens.typography[style.name] = this.extractTypographyValue(style);
        } else if (style.styleType === 'EFFECT') {
          // Handle shadows, etc.
        }
      });

      // Extract from document (fallback for styles not defined)
      this.extractTokensFromNode(file.document, tokens);

      let content = `## Design Tokens from ${file.name}\n\n`;

      if (Object.keys(tokens.colors).length > 0) {
        content += `### Colors\n\`\`\`css\n`;
        Object.entries(tokens.colors).forEach(([name, value]) => {
          content += `--${this.kebabCase(name)}: ${value};\n`;
        });
        content += `\`\`\`\n\n`;
      }

      if (Object.keys(tokens.typography).length > 0) {
        content += `### Typography\n\`\`\`css\n`;
        Object.entries(tokens.typography).forEach(([name, value]) => {
          content += `--${this.kebabCase(name)}: ${JSON.stringify(value)};\n`;
        });
        content += `\`\`\`\n\n`;
      }

      return {
        content: [
          {
            type: 'text',
            text: content,
          },
        ],
      };
    } catch (error) {
      throw new Error(`Failed to extract design tokens: ${error instanceof Error ? error.message : String(error)}`);
    }
  }

  private async handleAnalyzeComponents(args: any) {
    const { file_id } = args;

    try {
      const response = await figmaApi.get(`/files/${file_id}`);
      const file: FigmaFile = response.data;

      let content = `## Components in ${file.name}\n\n`;

      if (Object.keys(file.components).length === 0) {
        content += 'No components found in this file.\n';
      } else {
        Object.entries(file.components).forEach(([id, component]) => {
          content += `### ${component.name}\n`;
          content += `- **ID:** ${id}\n`;
          content += `- **Description:** ${component.description || 'No description'}\n`;

          if (component.componentPropertyDefinitions) {
            content += `- **Properties:**\n`;
            Object.entries(component.componentPropertyDefinitions).forEach(([propName, propDef]: [string, any]) => {
              content += `  - ${propName}: ${propDef.type}\n`;
            });
          }
          content += '\n';
        });
      }

      return {
        content: [
          {
            type: 'text',
            text: content,
          },
        ],
      };
    } catch (error) {
      throw new Error(`Failed to analyze components: ${error instanceof Error ? error.message : String(error)}`);
    }
  }

  private async handleGenerateVueComponent(args: any) {
    const { file_id, component_id, component_name } = args;

    try {
      // Get component data
      const fileResponse = await figmaApi.get(`/files/${file_id}`);
      const file: FigmaFile = fileResponse.data;

      const component = file.components[component_id];
      if (!component) {
        throw new Error(`Component ${component_id} not found`);
      }

      // Generate Vue component code
      const vueCode = this.generateVueCode(component, component_name, file);

      return {
        content: [
          {
            type: 'text',
            text: `## Generated Vue Component: ${component_name}\n\n\`\`\`vue\n${vueCode}\n\`\`\`\n\n**Component generated from Figma component "${component.name}"**`,
          },
        ],
      };
    } catch (error) {
      throw new Error(`Failed to generate Vue component: ${error instanceof Error ? error.message : String(error)}`);
    }
  }

  private async handleExportAssets(args: any) {
    const { file_id, format = 'PNG', scale = 1 } = args;

    try {
      // Get file to find image nodes
      const response = await figmaApi.get(`/files/${file_id}`);
      const file: FigmaFile = response.data;

      const imageNodes = this.findImageNodes(file.document);
      const nodeIds = imageNodes.map(node => node.id);

      if (nodeIds.length === 0) {
        return {
          content: [
            {
              type: 'text',
              text: 'No image assets found in this Figma file.',
            },
          ],
        };
      }

      // Get image URLs
      const imagesResponse = await figmaApi.get(`/images/${file_id}`, {
        params: {
          ids: nodeIds.join(','),
          format: format.toLowerCase(),
          scale,
        },
      });

      let content = `## Exported Assets from ${file.name}\n\n`;
      content += `**Format:** ${format} | **Scale:** ${scale}x\n\n`;

      Object.entries(imagesResponse.data.images).forEach(([nodeId, url]) => {
        const node = imageNodes.find(n => n.id === nodeId);
        content += `- **${node?.name || nodeId}**: ${url}\n`;
      });

      content += '\n*Note: Download these URLs to save the assets locally.*';

      return {
        content: [
          {
            type: 'text',
            text: content,
          },
        ],
      };
    } catch (error) {
      throw new Error(`Failed to export assets: ${error instanceof Error ? error.message : String(error)}`);
    }
  }

  // Helper methods
  private findNodeById(node: any, targetId: string): any {
    if (node.id === targetId) {
      return node;
    }

    if (node.children) {
      for (const child of node.children) {
        const found = this.findNodeById(child, targetId);
        if (found) return found;
      }
    }

    return null;
  }

  private findImageNodes(node: any): any[] {
    const images: any[] = [];

    if (node.type === 'RECTANGLE' && node.fills?.some((fill: any) => fill.type === 'IMAGE')) {
      images.push(node);
    }

    if (node.children) {
      node.children.forEach((child: any) => {
        images.push(...this.findImageNodes(child));
      });
    }

    return images;
  }

  private extractTokensFromNode(node: any, tokens: DesignToken) {
    // Extract colors from fills
    if (node.fills) {
      node.fills.forEach((fill: any) => {
        if (fill.type === 'SOLID') {
          const colorName = `color_${Math.floor(fill.color.r * 255)}_${Math.floor(fill.color.g * 255)}_${Math.floor(fill.color.b * 255)}`;
          tokens.colors[colorName] = `rgb(${Math.floor(fill.color.r * 255)}, ${Math.floor(fill.color.g * 255)}, ${Math.floor(fill.color.b * 255)})`;
        }
      });
    }

    // Extract spacing from layout
    if (node.absoluteBoundingBox) {
      // This is a simplified extraction - real implementation would be more sophisticated
    }

    if (node.children) {
      node.children.forEach((child: any) => this.extractTokensFromNode(child, tokens));
    }
  }

  private extractColorValue(style: any): string {
    // Simplified - would need to handle different style types
    return '#000000'; // Placeholder
  }

  private extractTypographyValue(style: any): any {
    // Simplified - would need to parse Figma typography styles
    return {
      fontFamily: 'Inter',
      fontSize: '16px',
      fontWeight: '400',
    };
  }

  private generateVueCode(component: any, componentName: string, file: FigmaFile): string {
    // This is a simplified Vue component generation
    // Real implementation would parse the component structure more thoroughly
    return `<template>
  <div class="${this.kebabCase(componentName)}">
    <!-- Generated component content -->
    <div class="component-content">
      ${component.name}
    </div>
  </div>
</template>

<script setup lang="ts">
// ${componentName} component generated from Figma
// Component ID: ${component.id}

const props = defineProps<{
  // Add component props based on Figma component properties
}>()

// Component logic here
</script>

<style scoped>
.${this.kebabCase(componentName)} {
  /* Styles would be extracted from Figma component */
}
</style>`;
  }

  private kebabCase(str: string): string {
    return str
      .replace(/([a-z])([A-Z])/g, '$1-$2')
      .replace(/[\s_]+/g, '-')
      .toLowerCase();
  }

  async run() {
    const transport = new StdioServerTransport();
    await this.server.connect(transport);
    console.error('Figma MCP server running...');
  }
}

// Start the server
const server = new FigmaMCPServer();
server.run().catch(console.error);
