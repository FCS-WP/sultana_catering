const fs = require("fs");
const path = require("path");
const webpack = require("webpack");
// Init Config Webpack
require("dotenv-extended").load();
const BrowserSyncPlugin = require("browser-sync-webpack-plugin");
// Css extraction and minification
const MiniCssExtractPlugin = require("mini-css-extract-plugin");
const CssMinimizerPlugin = require("css-minimizer-webpack-plugin");

// Clean out build dir in-between builds
const { CleanWebpackPlugin } = require("clean-webpack-plugin");

// Define Theme name
const themeName = process.env.THEME_NAME ? process.env.THEME_NAME : "zippy";
const childThemeDir = path.resolve(
  __dirname,
  "src",
  "wp-content",
  "themes",
  `${themeName}-child`
);
const localDomain = process.env.PROJECT_HOST
  ? process.env.PROJECT_HOST
  : "http://localhost:68";

const assetsDir = path.join(childThemeDir, "assets");
const jsDir = path.join(assetsDir, "js");
const sassDir = path.join(assetsDir, "sass");
const outputDir = path.join(assetsDir, "dist");

function readEntryNames(directory, extension) {
  if (!fs.existsSync(directory)) {
    return [];
  }

  return fs
    .readdirSync(directory)
    .filter((filename) => filename.endsWith(extension))
    .map((filename) => filename.slice(0, -extension.length));
}

function addEntry(entries, name, jsPath, scssPath) {
  const files = [];

  if (fs.existsSync(scssPath)) {
    files.push(scssPath);
  }

  if (fs.existsSync(jsPath)) {
    files.push(jsPath);
  }

  if (files.length > 0) {
    entries[name] = files;
  }
}

function buildEntries() {
  const entries = {};

  addEntry(
    entries,
    "app",
    path.join(jsDir, "app.js"),
    path.join(sassDir, "app.scss")
  );

  const pageJsDir = path.join(jsDir, "pages");
  const pageScssDir = path.join(sassDir, "pages");
  const pageNames = new Set([
    ...readEntryNames(pageJsDir, ".js"),
    ...readEntryNames(pageScssDir, ".scss"),
  ]);

  pageNames.forEach((pageName) => {
    addEntry(
      entries,
      pageName,
      path.join(pageJsDir, `${pageName}.js`),
      path.join(pageScssDir, `${pageName}.scss`)
    );
  });

  return entries;
}

const entries = buildEntries();
module.exports = [
  {
    stats: "minimal",
    entry: entries,
    output: {
      filename: "js/[name].min.js",
      path: outputDir,
    },
    module: {
      rules: [
        // js babelization
        {
          test: /\.(js|jsx)$/,
          exclude: /node_modules/,
          loader: "babel-loader",
        },
        // sass compilation
        {
          test: /\.(sass|scss)$/,
          use: [
            MiniCssExtractPlugin.loader,
            {
              loader: "css-loader",
              options: { url: false },
            },
            {
              loader: "postcss-loader",
              options: {
                sourceMap: true,
              },
            },
            {
              loader: "sass-loader",
              options: {
                api: "modern",
                sourceMap: true,
                sassOptions: {
                  outputStyle: "compressed",
                  includePaths: [path.resolve(__dirname, "node_modules")],
                },
              },
            },
          ],
        },
        // Font files
        {
          test: /\.(woff|woff2|ttf|otf)$/,
          loader: "file-loader",
          include: path.resolve(__dirname, "../"),
          options: {
            name: "[hash].[ext]",
            outputPath: "fonts/",
          },
        },
        // loader for images and icons (only required if css references image files)
        {
          test: /\.(png|jpg|gif)$/,
          type: "asset/resource",
          generator: {
            filename: "img/[name][ext]",
          },
        },
      ],
    },
    plugins: [
      new CleanWebpackPlugin({
        cleanOnceBeforeBuildPatterns: ["css/*", "js/*", "img/*", "fonts/*"],
      }),
      // css extraction into dedicated file
      new MiniCssExtractPlugin({
        filename: "css/[name].min.css",
      }),
      new webpack.ProvidePlugin({
        $: "jquery",
        jQuery: "jquery",
      }),
      new BrowserSyncPlugin(
        {
          proxy: localDomain,
          files: [
            path.join(outputDir, "css/*.css"),
            path.join(outputDir, "js/*.js"),
            path.join(childThemeDir, "**/*.php"),
          ],
          injectCss: true,
        },
        { reload: false }
      ),
    ],
    optimization: {
      // minification - only performed when mode = production
      minimizer: [
        // js minification - special syntax enabling webpack 5 default terser-webpack-plugin
        `...`,
        // css minification
        new CssMinimizerPlugin(),
      ],
    },
  },
];
