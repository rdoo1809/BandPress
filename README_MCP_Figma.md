# MCP Figma Integration Setup

This guide shows how to set up MCP (Model Context Protocol) integration with Cursor to enable AI-powered UI generation from Figma designs.

## Prerequisites

1. **Figma Personal Access Token**: Get one from https://www.figma.com/developers/api#access-tokens
2. **Node.js 18+**: Required for running the MCP server
3. **Cursor**: Latest version with MCP support

## Setup Steps

### 1. Configure Figma Access Token

Edit `mcp-figma-server/config.env` and replace the placeholder:

```bash
# Replace this line:
FIGMA_ACCESS_TOKEN=YOUR_FIGMA_ACCESS_TOKEN_HERE

# With your actual token:
FIGMA_ACCESS_TOKEN=figd_your_actual_token_here
```

### 2. Install MCP Server Dependencies

```bash
cd mcp-figma-server
npm install
npm run build
```

### 3. Configure Cursor

Add the MCP server configuration to your Cursor settings. You can either:

**Option A: Use the provided config file**
- Open Cursor settings
- Go to MCP section
- Copy the contents of `cursor-figma-mcp-config.json`

**Option B: Manual configuration**
Add this to your Cursor MCP servers configuration:

```json
{
  "mcpServers": {
    "figma-design-server": {
      "command": "node",
      "args": ["path/to/your/band-press/mcp-figma-server/dist/index.js"],
      "cwd": "path/to/your/band-press/mcp-figma-server",
      "env": {
        "NODE_ENV": "production"
      }
    }
  }
}
```

Replace `path/to/your/band-press` with your actual project path.

### 4. Test the Integration

1. Restart Cursor to load the MCP server
2. Open a new chat or use the agent
3. Test with a simple command like:

```
Read my Figma file with ID: YOUR_FIGMA_FILE_ID
```

You should see the agent use the MCP tools to analyze your Figma file.

## Available MCP Tools

The server provides these tools that Cursor's agent can use:

### `read_figma_file`
- **Purpose**: Read and analyze Figma file structure
- **Usage**: "Analyze the structure of Figma file abc123"
- **Returns**: File overview, components, and styles

### `extract_design_tokens`
- **Purpose**: Extract design system tokens
- **Usage**: "Extract design tokens from Figma file abc123"
- **Returns**: Colors, typography, spacing, and other design tokens

### `analyze_components`
- **Purpose**: Analyze reusable components
- **Usage**: "Show me all components in Figma file abc123"
- **Returns**: Component inventory with properties

### `generate_vue_component`
- **Purpose**: Generate Vue component code from Figma designs
- **Usage**: "Create a Vue component from the login button in Figma file abc123"
- **Returns**: Complete Vue component code with TypeScript

### `export_figma_assets`
- **Purpose**: Export images and assets
- **Usage**: "Export all images from Figma file abc123 as PNG"
- **Returns**: Download URLs for assets

## Example Usage

Once configured, you can ask the agent things like:

```
"Create a Vue component based on the header design in my Figma file fig123:abc456"
"Extract all the colors and typography from my design system file"
"Show me the structure of my landing page design"
"Generate Vue code for the user profile card component"
```

## Troubleshooting

### Server Won't Start
- Check that `config.env` has a valid Figma token
- Ensure Node.js 18+ is installed
- Run `npm run build` in the MCP server directory

### Cursor Can't Connect
- Verify the path in Cursor config points to the correct `dist/index.js`
- Check Cursor logs for connection errors
- Restart Cursor after configuration changes

### Figma API Errors
- Verify your access token has read permissions
- Check that the Figma file is accessible to your token
- Ensure file IDs are correct (from Figma URL)

## Development

To modify the MCP server:

```bash
cd mcp-figma-server
npm run dev  # For development with auto-reload
npm run build  # For production build
```

The server code is in `src/index.ts` and includes comprehensive error handling and Figma API integration.

## Security Notes

- Never commit your Figma access token to version control
- The token is only used for read operations on your Figma files
- Consider using a dedicated Figma account with limited access for this integration
