var config = {
    paths: {
        angular: 'Magezon_Builder/vendor/angular/angular',
        dndLists: 'Magezon_Builder/vendor/angular-drag-and-drop-lists/angular-drag-and-drop-lists',
        magezonBuilder: 'Magezon_Builder/js/builder',
        formly: 'Magezon_Builder/vendor/angular-formly/dist/formly',
        formlyBootstrap: 'angular-formly-templates-bootstrap',
        uiBootstrap: 'Magezon_Builder/js/ui-bootstrap-tpls-2.5.0.min',
        angularMocks: 'Magezon_Builder/vendor/angular-mocks/angular-mocks',
        angularChronicle: 'Magezon_Builder/vendor/chronicle/chronicle',
        'api-check': 'Magezon_Builder/vendor/api-check/dist/api-check',
        formlyUtils: 'Magezon_Builder/js/factories/FormlyUtils',
        angularSanitize : 'Magezon_Builder/vendor/angular-sanitize/angular-sanitize',
        dynamicDirective: 'Magezon_Builder/js/modules/dynamicDirective',
        outsideClickDirective: 'Magezon_Builder/js/modules/outside-click',
        owlcarouselDirective: 'Magezon_Builder/js/modules/angular-owl-carousel-2',
        minicolors: 'Magezon_Builder/js/minicolors.min',
        mgzUiTinymce: 'Magezon_Builder/vendor/angular-ui-tinymce/src/tinymce',
        codemirror: 'Magezon_Builder/vendor/codemirror/lib/codemirror',
        codemirrorCss: 'Magezon_Builder/vendor/codemirror/mode/css/css',
        uiCodemirror: 'Magezon_Builder/vendor/angular-ui-codemirror/ui-codemirror',
        uiSelect: 'Magezon_Builder/vendor/angular-ui-select/dist/select.min',
        ngStats: 'Magezon_Builder/vendor/ng-stats/ng-stats',
        staticInclude: 'Magezon_Builder/js/directives/staticInclude'
    },
    shim: {
        angular: {
            exports: 'angular'
        },
        angularMocks: {
            deps: ['angular']
        },
        dndLists: {
            deps: ['angular']
        },
        uiBootstrap: {
            deps: ['angular']
        },
        angularChronicle: {
            deps: ['angular']
        },
        angularSanitize: {
            deps: ['angular']
        },
        dynamicDirective: {
            deps: ['angular']
        },
        outsideClickDirective: {
            deps: ['angular']
        },
        owlcarouselDirective: {
            deps: ['angular']
        },
        mgzUiTinymce: {
            deps: ['angular']
        },
        codemirror: {
            exports: 'CodeMirror'
        },
        uiCodemirror: {
            deps: ['codemirror', 'angular']
        },
        uiSelect: {
            deps: ['angular']
        },
        ngStats: {
            deps: ['angular']
        },
        staticInclude: {
            deps: ['angular']
        },
        formly: {
            deps: ['jquery']
        },
        'Magezon_Builder/js/carousel': {
            deps: ['jquery']
        }
    }
};