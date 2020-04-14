// Add these line near the end of satellizer.min.js
// Save refresh token from server
response:function(e){var a=e.headers(t.authHeader);if(a!==null&&t.httpInterceptor(e)){n.setToken({access_token:a.split(" ")[1]});}return e;},
responseError:function(m){var a=m.headers(t.authHeader);if(a!==null&&t.httpInterceptor(m)){n.setToken({access_token:a.split(" ")[1]});}return e.reject(m)}

// Raw
response : function (e) {
	var a = e.headers(t.authHeader);
	if (a !== null && t.httpInterceptor(e)) {
		n.setToken({
			access_token : a.split(" ")[1]
		});
	}
	return e;
},
responseError : function (m) {
        var a = m.headers(t.authHeader);
        if (a !== null && t.httpInterceptor(m)) {
                n.setToken({
                        access_token : a.split(" ")[1]
                });
        }
        return e.reject(m)
}