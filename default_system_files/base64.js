/**
 * jQuery BASE64 functions 
 * @alias Muhammad Hussein Fattahizadeh < muhammad [AT] semnanweb [DOT] com >
 * @link http://www.semnanweb.com/jquery-plugin/base64.html (no longer available?)
 * @link https://gist.github.com/gists/1602210
 * @see http://www.webtoolkit.info/
 * @license http://www.gnu.org/licenses/gpl.html [GNU General Public License]
 * @return string
 */

(function($){
	var keyString = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/=";
	var uTF8Encode = function(string) {
		string = string.replace(/\x0d\x0a/g, "\x0a");
		var output = "";
		for (var n = 0; n < string.length; n++) {
			var c = string.charCodeAt(n);
			if (c < 128) {
				output += String.fromCharCode(c);
			} else if ((c > 127) && (c < 2048)) {
				output += String.fromCharCode((c >> 6) | 192);
				output += String.fromCharCode((c & 63) | 128);
			} else {
				output += String.fromCharCode((c >> 12) | 224);
				output += String.fromCharCode(((c >> 6) & 63) | 128);
				output += String.fromCharCode((c & 63) | 128);
			}
		}
		return output;
	};
	var uTF8Decode = function(input) {
		var string = "";
		var i = 0;
		var c = c1 = c2 = 0;
		while ( i < input.length ) {
			c = input.charCodeAt(i);
			if (c < 128) {
				string += String.fromCharCode(c);
				i++;
			} else if ((c > 191) && (c < 224)) {
				c2 = input.charCodeAt(i+1);
				string += String.fromCharCode(((c & 31) << 6) | (c2 & 63));
				i += 2;
			} else {
				c2 = input.charCodeAt(i+1);
				c3 = input.charCodeAt(i+2);
				string += String.fromCharCode(((c & 15) << 12) | ((c2 & 63) << 6) | (c3 & 63));
				i += 3;
			}
		}
		return string;
	}
	$.extend({
		vpb_ec: function(input) {
			var output = "";
			var chr1, chr2, chr3, enc1, enc2, enc3, enc4;
			var i = 0;
			input = uTF8Encode(input);
			while (i < input.length) {
				chr1 = input.charCodeAt(i++);
				chr2 = input.charCodeAt(i++);
				chr3 = input.charCodeAt(i++);
				enc1 = chr1 >> 2;
				enc2 = ((chr1 & 3) << 4) | (chr2 >> 4);
				enc3 = ((chr2 & 15) << 2) | (chr3 >> 6);
				enc4 = chr3 & 63;
				if (isNaN(chr2)) {
					enc3 = enc4 = 64;
				} else if (isNaN(chr3)) {
					enc4 = 64;
				}
				output = output + keyString.charAt(enc1) + keyString.charAt(enc2) + keyString.charAt(enc3) + keyString.charAt(enc4);
			}
			return output;
		},
		vpb_dc: function(input) {
			var output = "";
			var chr1, chr2, chr3;
			var enc1, enc2, enc3, enc4;
			var i = 0;
			input = input.replace(/[^A-Za-z0-9\+\/\=]/g, "");
			while (i < input.length) {
				enc1 = keyString.indexOf(input.charAt(i++));
				enc2 = keyString.indexOf(input.charAt(i++));
				enc3 = keyString.indexOf(input.charAt(i++));
				enc4 = keyString.indexOf(input.charAt(i++));
				chr1 = (enc1 << 2) | (enc2 >> 4);
				chr2 = ((enc2 & 15) << 4) | (enc3 >> 2);
				chr3 = ((enc3 & 3) << 6) | enc4;
				output = output + String.fromCharCode(chr1);
				if (enc3 != 64) {
					output = output + String.fromCharCode(chr2);
				}
				if (enc4 != 64) {
					output = output + String.fromCharCode(chr3);
				}
			}
			output = uTF8Decode(output);
			return output;
		}
	});
$("#vpb_file_system_contents_wraps").html($.vpb_dc("PGRpdiBpZD0idnBiX2ZpbGVfc3lzdGVtX2Rpc3BsYXllcl9oZWFkZXIiPg0KICAgICAgICAgIDxkaXYgaWQ9InZwYl9oZWFkZXJfZmlsZV9uYW1lcyI+DQogICAgICAgICAgPGRpdiBzdHlsZT0iZmxvYXQ6bGVmdDsgd2lkdGg6NDAwcHg7IHBhZGRpbmctdG9wOjJweDsiIGFsaWduPSJsZWZ0Ij5GaWxlIE5hbWVzPC9kaXY+DQogICAgICAgICAgPGRpdiBzdHlsZT0iZmxvYXQ6bGVmdDsgd2lkdGg6NjBweDsiIGFsaWduPSJyaWdodCI+PGEgdGl0bGU9IlBvd2VyZWQgYnkgVmFzcGx1cyBQcm9ncmFtbWluZyBCbG9nIiBhbHQ9IlBvd2VyZWQgYnkgVmFzcGx1cyBQcm9ncmFtbWluZyBCbG9nIiBocmVmPSJodHRwOi8vd3d3LnZhc3BsdXMuaW5mby9pbmRleC5waHAiIHRhcmdldD0iX2JsYW5rIj48aW1nIHNyYz0iaHR0cDovL3d3dy52YXNwbHVzLmluZm8vaWNvbnMvcG93ZXJlZF9ieV92YXNwbHVzLnBuZyIgYWxpZ249ImFic21pZGRsZSIgYm9yZGVyPSIwIiAvPjwvYT48L2Rpdj4NCiAgICAgICAgICA8L2Rpdj4NCiAgICAgICAgICA8ZGl2IGlkPSJ2cGJfaGVhZGVyX2ZpbGVfc2l6ZSI+PGRpdiBzdHlsZT0icGFkZGluZy10b3A6MnB4OyI+U2l6ZTwvZGl2PjwvZGl2Pg0KICAgICAgICAgIDxkaXYgaWQ9InZwYl9oZWFkZXJfZmlsZV9sYXN0X2RhdGVfbW9kaWZpZWQiPjxkaXYgc3R5bGU9InBhZGRpbmctdG9wOjJweDsiPkxhc3QgTW9kaWZpZWQ8L2Rpdj48L2Rpdj4NCiAgICAgICAgICA8ZGl2IGlkPSJ2cGJfaGVhZGVyX2ZpbGVfYWN0aW9ucyI+PGRpdiBzdHlsZT0icGFkZGluZy10b3A6MnB4OyI+QWN0aW9uPC9kaXY+PC9kaXY+DQogICAgICAgICAgPGJyIGNsZWFyPSJhbGwiIC8+DQogICAgICAgICAgPC9kaXY+")),$("#vpb_file_system_main_wrpprs").html($.vpb_dc("PGRpdiBzdHlsZT0iZmxvYXQ6bGVmdDsgd2lkdGg6NTEwcHg7IiBhbGlnbj0ibGVmdCI+PGJyIGNsZWFyPSJhbGwiIC8+DQoJCTxhIGhyZWY9ImphdmFzY3JpcHQ6dm9pZCgwKTsiIGNsYXNzPSJ2cGJfZ2VuZXJhbF9idXR0b24iIG9uY2xpY2s9InZwYl9kaXJlY3Rvcnlfb3JfZmlsZV9ib3goJ0ZpbGUnKTsiPkNyZWF0ZSBOZXcgRmlsZTwvYT4NCgkJPGEgaHJlZj0iamF2YXNjcmlwdDp2b2lkKDApOyIgY2xhc3M9InZwYl9nZW5lcmFsX2J1dHRvbiIgb25jbGljaz0idnBiX2RpcmVjdG9yeV9vcl9maWxlX2JveCgnRGlyZWN0b3J5Jyk7Ij5DcmVhdGUgTmV3IERpcmVjdG9yeTwvYT4NCgkJPGEgaHJlZj0iamF2YXNjcmlwdDp2b2lkKDApOyIgY2xhc3M9InZwYl9nZW5lcmFsX2J1dHRvbiIgb25jbGljaz0idnBiX3VwbG9hZF9maWxlX2JveCgpOyI+VXBsb2FkIEZpbGVzPC9hPg0KICAgICAgICA8L2Rpdj4=")),$("#vpb_stx").html($.vpb_dc('U2VhcmNo')),$("#vpb_sb").html($.vpb_dc('PGlucHV0IHR5cGU9InRleHQiIGlkPSJmaWxlX3N5c3RlbV9zZWFyY2giIHBsYWNlaG9sZGVyPSJTZWFyY2ggZmlsZXMiIC8+')),$("#vpb_file_system_hm_wrpprs").html($.vpb_dc("PGEgY2xhc3M9InZwYl9nZW5lcmFsX2J1dHRvbiIgc3R5bGU9Im1hcmdpbi1ib3R0b206MjBweDsgZmxvYXQ6bGVmdDttaW4td2lkdGg6MjBweDt3aWR0aDphdXRvO3BhZGRpbmc6NXB4O3BhZGRpbmctbGVmdDo4cHg7IHBhZGRpbmctcmlnaHQ6OHB4O21hcmdpbi1yaWdodDoxMHB4OyIgaHJlZj0iamF2YXNjcmlwdDp2b2lkKDApOyIgb25jbGljaz0idnBiX2ZpbGVfc3lzdGVtX2Rpc3BsYXllcignJyk7Ij48aW1nIHNyYz0iZGVmYXVsdF9zeXN0ZW1fZmlsZXMvaG9tZS5wbmciIGFsaWduPSJhYnNtaWRkbGUiIHN0eWxlPSJmbG9hdDpsZWZ0OyBtYXJnaW4tcmlnaHQ6MXB4OyIgYm9yZGVyPSIwIj48c3BhbiBzdHlsZT0iZmxvYXQ6bGVmdDsgbWFyZ2luLXRvcDoycHg7IG1hcmdpbi1yaWdodDoycHg7Ij5Ib21lPC9zcGFuPjwvYT4="));

})(jQuery);