# MCP Figma Server

An MCP (Model Context Protocol) server that enables Cursor's AI agent to work with Figma design files.

## Setup

1. Install dependencies:
   ```bash
   npm install
   ```

2. Configure your Figma access token:
   ```bash
   cp .env.template .env
   ```

   Edit `.env` and add your Figma personal access token:
   ```
   FIGMA_ACCESS_TOKEN=your_actual_figma_token_here
   ```

   Get your token from: https://www.figma.com/developers/api#access-tokens

3. Build the server:
   ```bash
   npm run build
   ```

4. Configure Cursor to use this MCP server (see main README)

## Available Tools

- `read_figma_file`: Read and analyze Figma file structure
- `extract_design_tokens`: Extract design system tokens (colors, typography, spacing)
- `analyze_components`: Parse reusable components and their properties
- `generate_vue_component`: Generate Vue component code from Figma designs
- `export_figma_assets`: Export images and assets from Figma files
