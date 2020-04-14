"use strict";

function readSingleFile(e) {
	var file = e.target.files[0];
	if (!file) {
		return;
	}
	var reader = new FileReader();
	reader.onload = function(e) {
		var har = JSON.parse(e.target.result);
		var entries = har.log.entries;
		var entriesLength = entries.length;
		var jsonLength;
		var i;
		var j;
		var k = -1;
		var m = -1;
		var n;
		var p;
		var q;
		var initialTime;
		var text;
		var json;
		var mime;
		var time;
		var date;
		var hour;
		var json;
		var arrHashTagQuote = [];
		var arrQuote = [];
		var quote = [];
		var checkIndex;
		var checkVN30;
		var checkMatched;
		var prev;
		var current;
		var transit;
		var status;
		var check = true;
		for (i = 0; i < entriesLength; i++) {
			initialTime = entries[i].time;
			if (initialTime != 0) {
				text = entries[i].response.content.text;
				mime = entries[i].response.content.mimeType;
				time = entries[i].response.headers[0].value.split(" ");
				date = time.slice(1, 4);
				hour = time.slice(4, 5);
				date = date.join("-");
				hour = hour.toString();
				if (text !== undefined && mime == "application/json") {
					json = JSON.parse(text);
					if (json !== "[]" && json !== undefined) {
						jsonLength= json.length;
						for (j = 0; j < jsonLength; j++) {
							if (json[j] !== null) {
								if (typeof json[j][1] == "string") {
									k++;
									arrQuote[k] = json[j][1].split("#");
									arrQuote[k].forEach(function(element, index, array) {
										element = element.substr(0, element.indexOf("|") + 1)+date+"|"+hour+"|"+element.substring(element.indexOf("|") + 1);
										checkIndex = element.indexOf("INDEX");
										checkVN30 = element.indexOf("VN30");
										checkMatched = element.indexOf("|4^");
										if (checkIndex != -1 || checkVN30 != -1 || checkMatched == -1) {
											element = 0;						
										}
										m++;
										quote[m] = element;
									});
								}
							}
						}
					}
				}
			}
		}
		quote.sort();
		quote = quote.filter( function(element, index, array) {
			return quote.indexOf(element) == index;
		});
		quote.shift();
		m = quote.length;
		for (n = 1; n < m; n++) {
			quote[n] = quote[n].split("|");
			current  = quote[n];
			q = current.length;
			prev = quote[n - 1];
			current[3] = current[3].replace("4^", "");
			current[3] = parseFloat(current[3]);
			if (current[4] != undefined) {
				for (p = 4; p < q; p++) {
					if (current[p].indexOf("^") != -1) {
						delete current[p];
					}
				}
			}
			if (current[4] == undefined) {
				current[4] == "ATx";
				if (current[5] != undefined) {
					delete current[5];					
				}
			}
			if (prev[0] == current[0]) {
				current[5] = current[3] - prev[3];
				if (prev[4] != undefined && current[4] == undefined) {
					current[4] = prev[4];					
				}		
			}
			current = current.slice(0, 6).toString();
			console.log(current);
		}	
	};
	reader.readAsText(file);
} 

function displayContents(contents) {
	var element = document.getElementById('file-content');
	element.innerHTML = contents;
}

document.getElementById('file-input')
.addEventListener('change', readSingleFile, false);