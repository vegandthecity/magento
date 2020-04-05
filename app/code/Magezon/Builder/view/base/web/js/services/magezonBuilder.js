define([
	'jquery',
	'angular',
	'Magezon_Builder/js/factories/FormlyUtils'
], function ($, angular, FormlyUtils) {

	var magezonBuilderService = function(elementManager, formlyConfig, $rootScope) {
		var self = this;
		self.data = {};
		self.cacheRequests = {};
		self.cacheData = {};
		self.directives = {};

		this.viewMode = 'xl';

		//https://developer.mozilla.org/en-US/docs/Web/API/WindowOrWorkerGlobalScope/btoa#Unicode_strings
		this.utoa = function(str) {
		    return window.btoa(unescape(encodeURIComponent(str)));
		}

		this.atou = function(str) {
		    return decodeURIComponent(escape(window.atob(str)));
		}

		this.setViewMode = function(viewMode) {
			this.viewMode = viewMode;
		}

		this.getViewMode = function(viewMode) {
			return this.viewMode;
		}

		this.getTargetElement = function() {
			return this.targetElement;
		}

		this.setTargetElement = function(targetElement) {
			this.targetElement = targetElement;
		}

		this.getTargetAction = function() {
			return this.targetAction;
		}

		this.setTargetAction = function(targetAction) {
			this.targetAction = targetAction;
		}

		this.getElementType = function(eleType) {
			var _types = eleType.split('-');
			var type = '';
			for (var i = 0; i < _types.length; i++) {
				var _types1 = _types[i].split('_');
				for (var x = 0; x < _types1.length; x++) {
					type += (_types1[x].charAt(0).toUpperCase() + _types1[x].substr(1));
				}
			}
			return type;
		}

		this.canReplace = function(element) {
			if (element) {
				var builderElement = elementManager.elements[element.type];
				// check for row with level 1)
				var rootSelector   = $('.' + element.id).parent().parent('.mgz-builder-content');
				if (!rootSelector.length && !builderElement.parent) {
					return true;
				}
			}
			return false;
		}

		this.getUrl = function(url) {
			if (url.indexOf('http') === -1) {
				url = $rootScope.builderConfig.baseUrl + url;
			}
			return url;
		}

        // Source: mage/adminhtml/wysiwyg/tiny_mce/tinymce4Adapter.js
        /**
         * Retrieve directives URL with substituted directive value.
         *
         * @param {String} directive
         */
		this.makeDirectiveUrl = function(directive) {
			return $rootScope.builderConfig.directives_url
                .replace(/directive/, 'directive/___directive/' + directive)
                .replace(/\/$/, '');
		}

		this.parseAttributesString = function (attributes) {
            var result = {};

            // Decode &quot; entity, as regex below does not support encoded quote
            attributes = attributes.replace(/&quot;/g, '"');
            attributes = attributes.replace(/'/g, '"');

            attributes.gsub(
                /(\w+)(?:\s*=\s*(?:(?:"((?:\\.|[^"])*)")|(?:'((?:\\.|[^'])*)')|([^>\s]+)))?/,
                function (match) {
                    result[match[1]] = match[2];
                }
            );

            return result;
        }

		/**
         * Convert {{directive}} style attributes syntax to absolute URLs
         * @param {Object} content
         * @return {*}
         */
        this.encodeDirectives = function (content) {
        	if (!content) return '';
        	var self = this;
            // collect all HTML tags with attributes that contain directives
            var result = content.gsub(/<([a-z0-9\-\_]+[^>]+?)([a-z0-9\-\_]+="[^"]*?\{\{.+?\}\}.*?".*?)>/i, function (match) {
                var attributesString = match[2],
                    decodedDirectiveString;

                // process tag attributes string
                attributesString = attributesString.gsub(/([a-z0-9\-\_]+)="(.*?)(\{\{.+?\}\})(.*?)"/i, function (m) {
                    decodedDirectiveString = encodeURIComponent(Base64.mageEncode(m[3].replace(/&quot;/g, '"')));

                    var newUrl;
                    if (m[3].indexOf('media') !== -1) {
                    	m[3].gsub(/\{\{media(.*?)\}\}/i, function (match, index) {
                    		var attributes = self.parseAttributesString(match[1]);
                    		if (attributes.url) {
                    			newUrl = self.getImageUrl(attributes.url);
                    		}
                    	});
                    }

                    if (newUrl) return m[1] + '="' + m[2] + newUrl + m[4] + '"';

                    return m[1] + '="' + m[2] + self.makeDirectiveUrl(decodedDirectiveString) + m[4] + '"';
                }.bind(this));

                return '<' + match[1] + attributesString + '>';
            }.bind(this));

            return this.wysiwygEncodeContent(result);
        }

        this.wysiwygDecodeContent = function(content) {
        	content = this.decodeWidgets(content);

            return content;
        }

        this.wysiwygEncodeContent = function (content) {
        	//content = this.encodeWidgets(this.decodeWidgets(content));
            content = this.removeDuplicateAncestorWidgetSpanElement(content);
            return content;
        }

        /**
         * Convert {{widget}} style syntax to image placeholder HTML
         * @param {String} content
         * @return {*}
         */
        this.encodeWidgets = function (content) {
        	var self = this;
            return content.gsub(/\{\{widget(.*?)\}\}/i, function (match) {
                var attributes = self.parseAttributesString(match[1]),
                    imageSrc,
                    imageHtml = '';

                if (attributes.type) {
                    attributes.type = attributes.type.replace(/\\\\/g, '\\');
                    imageSrc = config.placeholders[attributes.type];

                    if (config.types.indexOf(attributes['type_name']) > -1) {
                        imageHtml += '<span class="magento-placeholder magento-widget mceNonEditable" ' +
                            'contenteditable="false">';
                    } else {
                        imageSrc = config['error_image_url'];
                        imageHtml += '<span ' +
                            'class="magento-placeholder magento-placeholder-error magento-widget mceNonEditable" ' +
                            'contenteditable="false">';
                    }

                    imageHtml += '<img';
                    imageHtml += ' id="' + Base64.idEncode(match[0]) + '"';
                    imageHtml += ' src="' + imageSrc + '"';
                    imageHtml += ' />';

                    if (attributes['type_name']) {
                        imageHtml += attributes['type_name'];
                    }

                    imageHtml += '</span>';

                    return imageHtml;
                }
            });
        }

        /**
         * Convert image placeholder HTML to {{widget}} style syntax
         * @param {String} content
         * @return {*}
         */
        this.decodeWidgets = function (content) {
        	var self = this;
            return content.gsub(
                /(<span class="[^"]*magento-widget[^"]*"[^>]*>)?<img([^>]+id="[^>]+)>(([^>]*)<\/span>)?/i,
                function (match) {
                    var attributes = self.parseAttributesString(match[2]),
                        widgetCode;

                    if (attributes.id) {
                        widgetCode = Base64.idDecode(attributes.id);

                        if (widgetCode.indexOf('{{widget') !== -1) {
                            return widgetCode;
                        }
                    }

                    return match[0];
                }
            );
        }

        /**
         * Tinymce has strange behavior with html and this removes one of its side-effects
         * @param {String} content
         * @return {String}
         */
        this.removeDuplicateAncestorWidgetSpanElement = function (content) {
            var parser, doc;

            if (!window.DOMParser) {
                return content;
            }

            parser = new DOMParser();
            doc = parser.parseFromString(content.replace(/&quot;/g, '&amp;quot;'), 'text/html');

            [].forEach.call(doc.querySelectorAll('.magento-widget'), function (widgetEl) {
                var widgetChildEl = widgetEl.querySelector('.magento-widget');

                if (!widgetChildEl) {
                    return;
                }

                [].forEach.call(widgetEl.childNodes, function (el) {
                    widgetEl.parentNode.insertBefore(el, widgetEl);
                });

                widgetEl.parentNode.removeChild(widgetEl);
            });

            return doc.body ? doc.body.innerHTML.replace(/&amp;quot;/g, '&quot;') : content;
        }

		/**
         * Convert absolute URLs to {{directive}} style attributes syntax
         * @param {Object} content
         * @return {*}
         */
        this.decodeDirectives = function (content) {
            var directiveUrl = this.makeDirectiveUrl('%directive%').split('?')[0], // remove query string from directive
                // escape special chars in directives url to use in regular expression
                regexEscapedDirectiveUrl = directiveUrl.replace(/([$^.?*!+:=()\[\]{}|\\])/g, '\\$1'),
                regexDirectiveUrl = regexEscapedDirectiveUrl
                    .replace(
                        '%directive%',
                        '([a-zA-Z0-9,_-]+(?:%2[A-Z]|)+\/?)(?:(?!").)*'
                    ) + '/?(\\\\?[^"]*)?', // allow optional query string
                reg = new RegExp(regexDirectiveUrl);

            var result = content.gsub(reg, function (match) {
                return Base64.mageDecode(decodeURIComponent(match[1]).replace(/\/$/, '')).replace(/"/g, '&quot;');
            });
            return this.wysiwygDecodeContent(result);
        }

		this.generateElementShortCode = function(elements) {
			return angular.toJson(elements).replace('\’', "'");
			return this.utoa(angular.toJson(elements).replace('\’', "'"));
		}

		this.getBuilderConfig = function(requestKey, callbackFunction) {
			var self = this;
			if (!self.data.hasOwnProperty(requestKey)) {
				if (!self.cacheRequests.hasOwnProperty(requestKey)) {
					$.ajax({
		                url: this.getUrl('mgzbuilder/ajax/loadConfig'),
		                type:'POST',
		                data: {
		                	area: $rootScope.builderConfig.area,
		                	handle: $rootScope.builderConfig.handle,
		                	key: requestKey,
		                	class: $rootScope.builderConfig.builderClass
		                },
		            	dataType: 'json',
		                success: function(data) {
	                		self.data[requestKey] = data.config;
	                		self.processBuilderConfig(requestKey, data.config);
		                }
		            });
				}
				if (!self.cacheRequests.hasOwnProperty(requestKey)) self.cacheRequests[requestKey] = [];
				self.cacheRequests[requestKey].push(callbackFunction);
			} else {
				callbackFunction(self.data[requestKey]);
			}
		}

		this.processFormFields = function(fields, callbackFunction) {
			var self = this;
			var excludedFields  = ['defaultOptions'];
			var requireElements = [];

			var processRequireElements = function(children) {
				angular.forEach(children, function(elem) {
					if (elem.data && elem.data.element && $.inArray(elem.data.element, requireElements)===-1) {
						requireElements.push(elem.data.element);
					}
					if (elem.fieldGroup) {
						processRequireElements(elem.fieldGroup);
					}
					if (elem.templateOptions.children) {
						processRequireElements(elem.templateOptions.children);
					}
				});
			}
			processRequireElements(fields);

			var extendProperties = ['templateOptions'];
			var mergeElement = function(array, element, data) {
				for (var i = 0; i < array.length; i++) {
					var row = array[i];
					if (row['data'] && row['data']['element'] && row['data']['element'] == element) {
						_.each(extendProperties, function(property) {
							_.each(data[property], function(value, key) {
								if (!row[property].hasOwnProperty(key)) {
									row[property][key] = value;
								}
							});
							delete data[property];
						});
						_.each(data, function(value, key) {
							row[key] = value;
						});
					}
						
					for (var z = 0; z < excludedFields.length; z++) {
						delete row[excludedFields[z]];
					}

					if (row.hasOwnProperty('fieldGroup')) {
						mergeElement(row['fieldGroup'], element, data);
					}
					if (row.hasOwnProperty('fields')) {
						mergeElement(row['fields'], element, data);
					}
					if (row.templateOptions.children) {
						mergeElement(row.templateOptions.children, element, data);
					}
				}
			}

			if (requireElements.length) {
				var loaded = 0;
				_.each(requireElements, function(element, index) {
					require([element], function(data) {
						mergeElement(fields, element, data);
						loaded++;
						if (loaded == requireElements.length) {
	                		callbackFunction(fields);
						}
					});
				});
			} else {
	            callbackFunction(fields);
			}
		}

		this.hasCacheReques = function(requestKey) {
			return this.cacheRequests.hasOwnProperty(requestKey);
		}

		this.processBuilderConfig = function(requestKey, result) {
		    angular.forEach(self.cacheRequests[requestKey], function(callbackFunction) {
		    	if (angular.isFunction(callbackFunction)) {
		    		callbackFunction(result);
		    	} else {
		    		callbackFunction = result;
		    	}
		    });
		}

		this.getViewFileUrl = function(file) {
			if (file.indexOf('http') === -1) {
				return $rootScope.builderConfig.viewFileUrl + file;
			} else {
				return file;
			}
		}

		this.getImageUrl = function(file) {
			if (file && (file.indexOf('http:://') === -1 || file.indexOf('https://') === -1)) {
				return $rootScope.builderConfig.mediaUrl + file;
			} else {
				return file;
			}
		}

		this.saveToCache = function(key, data) {
			this.data[key] = data;
		}

		this.getFromCache = function(key) {
			if (this.data.hasOwnProperty(key)) {
				return this.data[key];
			}
			return false;
		}

		this.getData = function() {
			return this.data;
		}

		this.registerTypes = function(array) {
			for (var i = 0; i < array.length; i++) {
				var elem = array[i];
				// DynamicRows
				if (elem.templateOptions.children) {
					this.registerTypes(elem.templateOptions.children, formlyConfig);
				}
				if (elem.hasOwnProperty('fieldGroup')) {
					formlyConfig.setWrapper({
						name: elem.wrapper,
						templateUrl: elem.templateOptions.templateUrl
					});
					this.registerTypes(elem['fieldGroup'], formlyConfig);
				} else {
					var newType = {
						name: elem.type,
						templateUrl: elem.templateOptions.templateUrl
					};
					if (elem.defaultOptions) {
						newType.defaultOptions = Object.assign({}, elem.defaultOptions);
					}
					formlyConfig.setType(newType);
				}
				if (elem.templateOptions.wrapperTemplateUrl) {
					formlyConfig.setWrapper({
						name: elem.data.wrapperType,
						templateUrl: elem.templateOptions.wrapperTemplateUrl
					});
				}
			}
		}

	    /**
	     * Processing options list
	     *
	     * @param {Array} array - Property array
	     * @param {String} separator - Level separator
	     * @param {Array} created - list to add new options
	     *
	     * @return {Array} Plain options list
	     */
	    this.flattenCollection = function(array, separator, created) {
	        var i = 0,
	            length,
	            childCollection;

	        array = _.compact(array);
	        length = array.length;
	        created = created || [];

	        for (i; i < length; i++) {
	            created.push(array[i]);

	            if (array[i].hasOwnProperty(separator)) {
	                childCollection = array[i][separator];
	                delete array[i][separator];
	                this.flattenCollection.call(this, childCollection, separator, created);
	            }
	        }

	        return created;
	    }

	    /**
	     * Set levels to options list
	     *
	     * @param {Array} array - Property array
	     * @param {String} separator - Level separator
	     * @param {Number} level - Starting level
	     * @param {String} path - path to root
	     *
	     * @returns {Array} Array with levels
	     */
	    this.setProperty = function(array, separator, level, path) {
	        var i = 0,
	            length,
	            nextLevel,
	            nextPath;

	        array = _.compact(array);
	        length = array.length;
	        level = level || 0;
	        path = path || '';

	        for (i; i < length; i++) {
	            if (array[i]) {
	                _.extend(array[i], {
	                    level: level,
	                    path: path
	                });
	            }

	            if (array[i].hasOwnProperty(separator)) {
	                nextLevel = level + 1;
	                nextPath = path ? path + '.' + array[i].label : array[i].label;
	                this.setProperty.call(this, array[i][separator], separator, nextLevel, nextPath);
	            }
	        }

	        return array;
	    }

	    /**
	     * Preprocessing options list
	     *
	     * @param {Array} nodes - Options list
	     *
	     * @return {Object} Object with property - options(options list)
	     *      and cache options with plain and tree list
	     */
	    this.parseOptions = function(nodes) {
	        var caption,
	            value,
	            cacheNodes,
	            copyNodes;

	        nodes = this.setProperty(nodes, 'elements');
	        copyNodes = JSON.parse(JSON.stringify(nodes));
	        cacheNodes = this.flattenCollection(copyNodes, 'elements');

	        nodes = _.map(nodes, function (node) {
	            value = node.value;

	            if (value == null || value === '') {
	                if (_.isUndefined(caption)) {
	                    caption = node.label;
	                }
	            } else {
	                return node;
	            }
	        });

	        return {
	            options: _.compact(nodes),
	            cacheOptions: {
	                plain: _.compact(cacheNodes),
	                tree: _.compact(nodes)
	            }
	        };
	    }

	    this.uniqueid = function (size) {
	        var code = Math.random() * 25 + 65 | 0,
	            idstr = String.fromCharCode(code);

	        size = size || 12;

	        while (idstr.length < size) {
	            code = Math.floor(Math.random() * 42 + 48);

	            if (code < 58 || code > 64) {
	                idstr += String.fromCharCode(code);
	            }
	        }

	        return idstr.toLowerCase();
	    }

		this.diff = function (obj1, obj2, exclude) {
		    	var r = {};

			    if (!exclude) exclude = [];

			    for (var prop in obj1) {
			    	if (obj1.hasOwnProperty(prop)) {
			    		if (prop.startsWith("$") || prop == 'elements') continue;

			    		if (exclude.indexOf(obj1[prop]) == -1) {

		                // check if obj2 has prop
		                if (!obj2.hasOwnProperty(prop)) r[prop] = obj1[prop];

		                // check if prop is object, if so, recursive diff
		                else if (obj1[prop] === Object(obj1[prop])) {
		                	if (obj2[prop] == undefined || obj2[prop] == null)
		                		r[prop] = obj2[prop];
		                	else {
		                		var difference = self.diff(obj1[prop], obj2[prop]);
		                		if (Object.keys(difference).length > 0) r[prop] = difference;
		                	}
		                }

		                // check if obj1 and obj2 are equal
		                else if (obj1[prop] !== obj2[prop]) {
		                	if (obj1[prop] === undefined)
		                		r[prop] = 'undefined';
		                	if (obj1[prop] === null)
		                		r[prop] = null;
		                	else if (typeof obj1[prop] === 'function')
		                		r[prop] = 'function';
		                	else if (typeof obj1[prop] === 'object')
		                		r[prop] = 'object';
		                	else
		                		r[prop] = obj1[prop];
		                }
		            }
		        }
		    }
		    return r;
		}

		this.isBackend = function () {
			return $('#html-body').length ? true : false;
		}
	}

	return magezonBuilderService;
});