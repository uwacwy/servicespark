const path = require("path");
const webpack = require("webpack");

const TsconfigPathsPlugin = require("tsconfig-paths-webpack-plugin");

module.exports = {
	entry: {
		ServiceSpark: ["./src/ServiceSpark.ts", "angular", "@uirouter/angularjs"]
	},
	module: {
		rules: [
			{
				test: /\.tsx?$/,
				use: "ts-loader",
				exclude: /node_modules/
			}
		]
	},
	resolve: {
		extensions: [".tsx", ".ts", ".js"],
		plugins: [new TsconfigPathsPlugin()]
	},
	output: {
		filename: "[name].bundle.js",
		chunkFilename: "[name].bundle.js",
		path: path.resolve(__dirname, "dist")
	}
	//,
	// optimization: {
	//     splitChunks: {
	//         cacheGroups: {
	//             commons: {
	//                 test: /[\\/]node_modules[\\/]/,
	//                 name: "Vendor",
	//                 chunks: "all"
	//             }
	//         }
	//     }
	// }
};
