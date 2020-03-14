/******/ (function(modules) { // webpackBootstrap
/******/ 	// The module cache
/******/ 	var installedModules = {};
/******/
/******/ 	// The require function
/******/ 	function __webpack_require__(moduleId) {
/******/
/******/ 		// Check if module is in cache
/******/ 		if(installedModules[moduleId]) {
/******/ 			return installedModules[moduleId].exports;
/******/ 		}
/******/ 		// Create a new module (and put it into the cache)
/******/ 		var module = installedModules[moduleId] = {
/******/ 			i: moduleId,
/******/ 			l: false,
/******/ 			exports: {}
/******/ 		};
/******/
/******/ 		// Execute the module function
/******/ 		modules[moduleId].call(module.exports, module, module.exports, __webpack_require__);
/******/
/******/ 		// Flag the module as loaded
/******/ 		module.l = true;
/******/
/******/ 		// Return the exports of the module
/******/ 		return module.exports;
/******/ 	}
/******/
/******/
/******/ 	// expose the modules object (__webpack_modules__)
/******/ 	__webpack_require__.m = modules;
/******/
/******/ 	// expose the module cache
/******/ 	__webpack_require__.c = installedModules;
/******/
/******/ 	// define getter function for harmony exports
/******/ 	__webpack_require__.d = function(exports, name, getter) {
/******/ 		if(!__webpack_require__.o(exports, name)) {
/******/ 			Object.defineProperty(exports, name, {
/******/ 				configurable: false,
/******/ 				enumerable: true,
/******/ 				get: getter
/******/ 			});
/******/ 		}
/******/ 	};
/******/
/******/ 	// define __esModule on exports
/******/ 	__webpack_require__.r = function(exports) {
/******/ 		Object.defineProperty(exports, '__esModule', { value: true });
/******/ 	};
/******/
/******/ 	// getDefaultExport function for compatibility with non-harmony modules
/******/ 	__webpack_require__.n = function(module) {
/******/ 		var getter = module && module.__esModule ?
/******/ 			function getDefault() { return module['default']; } :
/******/ 			function getModuleExports() { return module; };
/******/ 		__webpack_require__.d(getter, 'a', getter);
/******/ 		return getter;
/******/ 	};
/******/
/******/ 	// Object.prototype.hasOwnProperty.call
/******/ 	__webpack_require__.o = function(object, property) { return Object.prototype.hasOwnProperty.call(object, property); };
/******/
/******/ 	// __webpack_public_path__
/******/ 	__webpack_require__.p = "";
/******/
/******/
/******/ 	// Load entry module and return exports
/******/ 	return __webpack_require__(__webpack_require__.s = 0);
/******/ })
/************************************************************************/
/******/ ({

/***/ "../../node_modules/angular/angular.js":
/*!**********************************************************************!*\
  !*** /home/bkovach/servicespark/app/node_modules/angular/angular.js ***!
  \**********************************************************************/
/*! no static exports found */
/***/ (function(module, exports) {


/***/ }),

/***/ "../../node_modules/angular/index.js":
/*!********************************************************************!*\
  !*** /home/bkovach/servicespark/app/node_modules/angular/index.js ***!
  \********************************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

eval("__webpack_require__(/*! ./angular */ \"../../node_modules/angular/angular.js\");\nmodule.exports = angular;\n\n\n//# sourceURL=webpack:////home/bkovach/servicespark/app/node_modules/angular/index.js?");

/***/ }),

/***/ "./src/Events/DetailController.ts":
/*!****************************************!*\
  !*** ./src/Events/DetailController.ts ***!
  \****************************************/
/*! exports provided: DetailController */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, \"DetailController\", function() { return DetailController; });\nvar DetailController = /** @class */ (function () {\n    function DetailController() {\n        this.hello = \"World\";\n        console.log(\"DetailController init\");\n    }\n    return DetailController;\n}());\n\n\n\n//# sourceURL=webpack:///./src/Events/DetailController.ts?");

/***/ }),

/***/ "./src/ServiceSpark.ts":
/*!*****************************!*\
  !*** ./src/ServiceSpark.ts ***!
  \*****************************/
/*! no exports provided */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony import */ var angular__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! angular */ \"../../node_modules/angular/index.js\");\n/* harmony import */ var angular__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(angular__WEBPACK_IMPORTED_MODULE_0__);\n/* harmony import */ var _Events_DetailController__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./Events/DetailController */ \"./src/Events/DetailController.ts\");\n\n\nvar ServiceSpark_Events_Event = angular__WEBPACK_IMPORTED_MODULE_0__[\"module\"](\"ServiceSpark.Events.Event\", [])\n    .controller('DetailController', [_Events_DetailController__WEBPACK_IMPORTED_MODULE_1__[\"DetailController\"]]);\nvar ServiceSpark = angular__WEBPACK_IMPORTED_MODULE_0__[\"module\"](\"ServiceSpark\", ['ui.router', 'ServiceSpark.Events.Event'])\n    .config([\n    \"$stateProvider\",\n    function ($stateProvider) {\n        $stateProvider\n            .state('events', {\n            url: '/events',\n            abstract: true\n        })\n            .state('events.event', {\n            url: '/:eventId',\n            abstract: true,\n            resolve: {\n                event: [\"$stateParams\", \"$http\", function ($stateParams, $http) {\n                        return $http.get('/' + [\n                            'api',\n                            'events',\n                            $stateParams['eventId']\n                        ].join('/'));\n                    }]\n            },\n            controller: function ($state) {\n                $state.go('detail');\n            }\n        })\n            .state(\"events.event.view\", {\n            url: '/detail',\n            templateUrl: 'events.html',\n            controller: 'EventsController',\n            resolve: {}\n        });\n    }\n]);\n\n\n//# sourceURL=webpack:///./src/ServiceSpark.ts?");

/***/ }),

/***/ 0:
/*!***********************************!*\
  !*** multi ./src/ServiceSpark.ts ***!
  \***********************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

eval("module.exports = __webpack_require__(/*! ./src/ServiceSpark.ts */\"./src/ServiceSpark.ts\");\n\n\n//# sourceURL=webpack:///multi_./src/ServiceSpark.ts?");

/***/ })

/******/ });