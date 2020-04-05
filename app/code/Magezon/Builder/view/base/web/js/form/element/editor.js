define([
    'jquery',
    'mage/adminhtml/wysiwyg/tiny_mce/html5-schema'
], function($, html5Schema) {

    var uniqueid = function (size) {
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
    };

    return {

        controller: function($rootScope, $scope, uiTinymceConfig, magezonBuilderService) {

            // TINYMCE3
            $scope.id      = uniqueid();
            $scope.schema  = html5Schema;

            if (typeof tinyMceEditors == 'undefined') {
                tinyMceEditors = $H({});
            }

            // retrieve directives URL with substituted directive value
            $scope.makeDirectiveUrl = function(directive) {
                return $rootScope.builderConfig.directives_url.replace('directive', 'directive/___directive/' + directive);
            }

            $scope.encodeDirectives = function(content) {
                if (!content) return;
                // collect all HTML tags with attributes that contain directives
                return content.gsub(/<([a-z0-9\-\_]+.+?)([a-z0-9\-\_]+=".*?\{\{.+?\}\}.*?".+?)>/i, function(match) {
                    var attributesString = match[2];

                    // process tag attributes string
                    attributesString = attributesString.gsub(/([a-z0-9\-\_]+)="(.*?)(\{\{.+?\}\})(.*?)"/i, function(m) {
                        return m[1] + '="' + m[2] + $scope.makeDirectiveUrl(Base64.mageEncode(m[3].replace(/&quot;/g, '"'))) + m[4] + '"';
                    }.bind(this));

                    return '<' + match[1] + attributesString + '>';

                }.bind(this));
            }
            $scope.content = $scope.encodeDirectives($scope.model[$scope.options.key]);

            $scope.updateTextArea = function () {
                var editor = window.tinyMCE.get($scope.id),
                    content;

                if (!editor) {
                    return;
                }
                content = editor.getContent();
                $scope.model[$scope.options.key] = content;
            }

            $scope.applySchema= function (editor) {
                var schema      = editor.schema,
                    schemaData  = $scope.schema,
                    makeMap     = window.tinyMCE.makeMap;

                jQuery.extend(true, {
                    nonEmpty: schema.getNonEmptyElements(),
                    boolAttrs: schema.getBoolAttrs(),
                    whiteSpace: schema.getWhiteSpaceElements(),
                    shortEnded: schema.getShortEndedElements(),
                    selfClosing: schema.getSelfClosingElements(),
                    blockElements: schema.getBlockElements()
                }, {
                    nonEmpty: makeMap(schemaData.nonEmpty),
                    boolAttrs: makeMap(schemaData.boolAttrs),
                    whiteSpace: makeMap(schemaData.whiteSpace),
                    shortEnded: makeMap(schemaData.shortEnded),
                    selfClosing: makeMap(schemaData.selfClosing),
                    blockElements: makeMap(schemaData.blockElements)
                });
            }

            $scope.onEditorInit = function (editor) {
                $scope.applySchema(editor);
            }

            $scope.setupTinymce3 = function(mode = 'exact') {
                var plugins = 'inlinepopups,safari,pagebreak,style,layer,table,advhr,advimage,emotions,iespell,media,searchreplace,contextmenu,paste,directionality,fullscreen,noneditable,visualchars,nonbreaking,xhtmlxtras',
                self = this;

                if ($scope.to.wysiwyg.widget_plugin_src) {
                    plugins = 'magentowidget,' + plugins;
                }

                var magentoPluginsOptions = $H({});
                var magentoPlugins = '';

                if ($scope.to.wysiwyg.plugins) {
                    $scope.to.wysiwyg.plugins.each(function(plugin) {
                        magentoPlugins = plugin.name + ',' + magentoPlugins;
                        magentoPluginsOptions.set(plugin.name, plugin.options);
                    });
                    if (magentoPlugins) {
                        plugins = '-' + magentoPlugins + plugins;
                    }
                }

                var settings = {
                    'entity_encoding': 'raw',
                    mode: (mode != undefined ? mode : 'none'),
                    elements: $scope.id,
                    theme: 'advanced',
                    plugins: plugins,
                    theme_advanced_buttons1: magentoPlugins + 'magentowidget,bold,italic,underline,strikethrough,|,justifyleft,justifycenter,justifyright,justifyfull,|,styleselect,formatselect,fontselect,fontsizeselect',
                    theme_advanced_buttons2: 'cut,copy,paste,pastetext,pasteword,|,search,replace,|,bullist,numlist,|,outdent,indent,blockquote,|,undo,redo,|,link,unlink,anchor,image,cleanup,help,code,|,forecolor,backcolor',
                    theme_advanced_buttons3: 'tablecontrols,|,hr,removeformat,visualaid,|,sub,sup,|,charmap,iespell,media,advhr,|,ltr,rtl,|,fullscreen',
                    theme_advanced_buttons4: 'insertlayer,moveforward,movebackward,absolute,|,styleprops,|,cite,abbr,acronym,del,ins,attribs,|,visualchars,nonbreaking,pagebreak',
                    theme_advanced_toolbar_location: 'top',
                    theme_advanced_toolbar_align: 'left',
                    theme_advanced_statusbar_location: 'bottom',
                    valid_elements: this.schema.validElements.join(','),
                    valid_children: this.schema.validChildren.join(','),
                    theme_advanced_resizing: true,
                    theme_advanced_resize_horizontal: false,
                    convert_urls: false,
                    relative_urls: false,
                    content_css: $scope.to.wysiwyg.content_css,
                    custom_popup_css: $scope.to.wysiwyg.popup_css,
                    magentowidget_url: $scope.to.wysiwyg.widget_window_url,
                    magentoPluginsOptions: magentoPluginsOptions,
                    doctype: '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">',
                    setup: function(ed){
                        ed.onInit.add($scope.onEditorInit.bind($scope));

                        ed.onSubmit.add(function(ed, e) {
                            varienGlobalEvents.fireEvent('tinymceSubmit', e);
                        });

                        ed.onPaste.add(function(ed, e, o) {
                            varienGlobalEvents.fireEvent('tinymcePaste', o);
                        });

                        ed.onBeforeSetContent.add(function(ed, o) {
                            varienGlobalEvents.fireEvent('tinymceBeforeSetContent', o);
                        });

                        ed.onSetContent.add(function(ed, o) {
                            varienGlobalEvents.fireEvent('tinymceSetContent', o);
                            $scope.updateTextArea();
                        });

                        ed.onSaveContent.add(function(ed, o) {
                            varienGlobalEvents.fireEvent('tinymceSaveContent', o);
                            $scope.updateTextArea();
                        });

                        var onChange = function(ed, l) {
                            varienGlobalEvents.fireEvent('tinymceChange', l);
                            $scope.updateTextArea();
                        };

                        ed.onChange.add(onChange);
                        ed.onKeyUp.add(onChange);

                        ed.onExecCommand.add(function(ed, cmd, ui, val) {
                            varienGlobalEvents.fireEvent('tinymceExecCommand', cmd);
                            $scope.updateTextArea();
                        });
                    }
                };

                // Set the document base URL
                if ($scope.to.wysiwyg.document_base_url) {
                    settings.document_base_url = $scope.to.wysiwyg.document_base_url;
                }

                if ($scope.to.wysiwyg.files_browser_window_url) {
                    settings['file_browser_callback'] = function (fieldName, url, objectType, w) {
                        $scope.openFileBrowser3({
                            win: w,
                            type: objectType,
                            field: fieldName
                        });
                    }.bind(this);
                }

                if ($scope.to.wysiwyg.width) {
                    settings.width = $scope.to.wysiwyg.width;
                }

                if ($scope.to.wysiwyg.height) {
                    settings.height = $scope.to.wysiwyg.height;
                }

                if ($scope.to.wysiwyg.settings) {
                    Object.extend(settings, $scope.to.wysiwyg.settings)
                }
                setTimeout(function() {
                    $scope.settings = settings;
                    window.tinyMCE.init(settings);
                    //window.tinyMCE.execCommand('mceAddControl', false, $scope.id);
                }, 800);

                return settings;
            }

            $scope.setupTinymce4 = function () {
                var settings;
                var deferreds = [];

                $scope.eventBus = new window.varienEvents();

                settings = {
                    fontsize_formats: $scope.to.wysiwyg.tinymce4.fontsize_formats,
                    lineheight_formats: "1.0 1.15 1.5 2.0 2.5 3.0",
                    theme: 'modern',
                    'entity_encoding': 'raw',
                    'convert_urls': false,
                    'content_css': $scope.to.wysiwyg.tinymce4['content_css'],
                    'relative_urls': true,
                    'verify_html': false,
                    menubar: false,
                    plugins: $scope.to.wysiwyg.tinymce4.plugins,
                    toolbar: $scope.to.wysiwyg.tinymce4.toolbar,
                    adapter: this,
                    setup: function(editor) {
                        editor.on('change', function(a1) {
                            var value = window.tinyMCE.get(a1.target.id).getContent();
                            $scope.model[$scope.options.key] = magezonBuilderService.decodeDirectives(value);
                        })
                    }
                };

                if ($scope.to.wysiwyg.baseStaticUrl && $scope.to.wysiwyg.baseStaticDefaultUrl) {
                    settings['document_base_url'] = $scope.to.wysiwyg.baseStaticUrl;
                }
                // Set the document base URL
                if ($scope.to.wysiwyg['document_base_url']) {
                    settings['document_base_url'] = $scope.to.wysiwyg['document_base_url'];
                }

                if ($scope.to.wysiwyg['files_browser_window_url']) {
                    /**
                     * @param {*} fieldName
                     * @param {*} url
                     * @param {*} objectType
                     * @param {*} w
                     */
                    settings['file_browser_callback'] = function (fieldName, url, objectType, w) {
                        $scope.openFileBrowser4({
                            win: w,
                            type: objectType,
                            field: fieldName
                        });
                    }.bind(this);
                }

                if ($scope.to.wysiwyg.width) {
                    settings.width = $scope.to.wysiwyg.width;
                }

                if ($scope.to.wysiwyg.height) {
                    settings.height = $scope.to.wysiwyg.height;
                }

                if ($scope.to.wysiwyg.plugins) {
                    settings.magentoPluginsOptions = {};

                    _.each($scope.to.wysiwyg.plugins, function (plugin) {
                        settings.magentoPluginsOptions[plugin.name] = plugin.options;
                    });
                }

                if ($scope.to.wysiwyg.settings) {
                    Object.extend(settings, $scope.to.wysiwyg.settings);
                }

                var plugins = $scope.to.wysiwyg.tinymce4.plugins ? $scope.to.wysiwyg.tinymce4.plugins.split(' ') : [];
                var toolbar = $scope.to.wysiwyg.tinymce4.plugins ? $scope.to.wysiwyg.tinymce4.toolbar.split(' ') : [];

                if ($scope.to.wysiwyg.plugins) {
                    $scope.to.wysiwyg.plugins.forEach(function (plugin) {
                        var deferred;

                        if (plugins.indexOf(plugin.name) === -1) {
                            plugins.push(plugin.name);
                        }

                        if (toolbar.indexOf(plugin.name) === -1) {
                            toolbar.push('|', plugin.name);
                        }

                        if (!plugin.src) {
                            return;
                        }

                        deferred = $.Deferred();
                        deferreds.push(deferred);

                        require([plugin.src], function (factoryFn) {
                            if (typeof factoryFn === 'function') {
                                factoryFn(plugin.options);
                            }

                            window.tinyMCE.PluginManager.load(plugin.name, plugin.src);
                            deferred.resolve();
                        });

                        if (deferreds.length) {
                            jQuery.when.apply(jQuery, deferreds).done(function () {
                                $scope.$apply(function() {
                                    $scope.settings = settings;
                                });
                            });
                        }
                    });

                    if (!deferreds.length) {
                        $scope.settings = settings;
                    }
                } else {
                    $scope.settings = $scope.setupTinymce3();
                }

                settings['plugins'] = plugins.join(' ');
                settings['toolbar'] = toolbar.join(' ');

                return settings;
            }

            if ($scope.to.wysiwyg.tinymce4) {
                $scope.setupTinymce4();
            } else {
                tinyMceEditors.set($scope.id, $scope);
                $scope.setupTinymce3();
            }

            $scope.getMediaBrowserOpener = function() {
                return $scope.mediaBrowserOpener;
            }

            $scope.getMediaBrowserTargetElementId = function() {
                return $scope.mediaBrowserTargetElementId;
            }

            $scope.openFileBrowser4 = function (o) {
                var typeTitle = this.translate('Select Images'),
                    storeId = 0,
                    frameDialog = jQuery('div.mce-container[role="dialog"]');

                var id   = frameDialog.find('.mce-textbox').eq(0).attr('id');
                var wUrl = $scope.to.wysiwyg.files_browser_window_url +
                        'target_element_id/' + id + '/' +
                        'store/' + storeId + '/';
                this.mediaBrowserOpener = o.win;
                this.mediaBrowserTargetElementId = o.field;

                if (typeof o.type !== 'undefined' && o.type !== '') { //eslint-disable-line eqeqeq
                    wUrl = wUrl + 'type/' + o.type + '/';
                }

                frameDialog.hide();
                jQuery('#mce-modal-block').hide();

                require(['mage/adminhtml/browser'], function () {
                    MediabrowserUtility.openDialog(wUrl, false, false, typeTitle, {
                        /**
                         * Closed.
                         */
                        closed: function () {
                            frameDialog.show();
                            jQuery('#mce-modal-block').show();
                        }
                    });
                });
            }

            $scope.openFileBrowser3 = function(o) {
                var typeTitle,
                    storeId = 0,
                    frameDialog = jQuery(o.win.frameElement).parents('[role="dialog"]'),
                    wUrl = $scope.to.wysiwyg.files_browser_window_url +
                    'target_element_id/' + $scope.id + '/' +
                    'store/' + storeId + '/';

                this.mediaBrowserOpener = o.win;
                this.mediaBrowserTargetElementId = o.field;

                if (typeof(o.type) != 'undefined' && o.type != "") {
                    typeTitle = 'image' == o.type ? this.translate('Insert Image...') : this.translate('Insert Media...');
                    wUrl = wUrl + "type/" + o.type + "/";
                } else {
                    typeTitle = this.translate('Insert File...');
                }

                frameDialog.hide();
                jQuery('#mceModalBlocker').hide();

                MediabrowserUtility.openDialog(wUrl, false, false, typeTitle, {
                    closed: function() {
                        frameDialog.show();
                        jQuery('#mceModalBlocker').show();
                    }
                });
            }

            /**
             * @param {String} string
             * @return {String}
             */
            $scope.translate = function (string) {
                return jQuery.mage.__ ? jQuery.mage.__(string) : string;
            }
        }
    }
})