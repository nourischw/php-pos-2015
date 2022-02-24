require.config({
    baseUrl: "app/js/libs",
    paths: {
        jquery: "//ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min",
        app: "../app",
		jssha: "jssha/src/sha",
    },
    shim: {
        main: {
			deps: ["bootstrap"],
            exports: "Main"
        },
        bootstrap: {
            deps: ["jquery"],
            exports: "Bootstrap"
        },
        jssha: { 
            deps: ["jquery"],
            exports: "JSSHA"
        }
    },
    waitSeconds: 15
});