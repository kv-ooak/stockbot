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
		var listvalueLength;
		var i;
		var j;
		var k = -1;
		var m;
		var n;
		var iniTime;
		var text;
		var json;
		var mime;
		var time;
		var date;
		var year;
		var month;
		var day;
		var hour;
		var minute;
		var second;
		var listValue;
		var quote = [];
		var subQuote = [];
		var current;
		var next;
		var prev;
		var ticker;
		var prev_ticker;
		var next_ticker;
		var bid;
		var prev_bid;
		var ask;
		var prev_ask;
		var price;
		var vol;
		var total;
		var prev_total;
		var next_total;
		var check = true;
		function twoDigits(number) {
			return number > 9 ? number : "0" + number;
		}
		for (i = 0; i < entriesLength; i++) {
			iniTime = entries[i].time;
			if (iniTime != 0) {
				text = entries[i].response.content.text;
				mime = entries[i].response.content.mimeType;
				time = new Date(entries[i].response.headers[0].value);
				if (time) {
					year 	= time.getFullYear().toString();
					month 	= time.getMonth() + 1;
					month 	= month.toString();
					day 	= time.getDate();
					hour 	= time.getHours();
					minute 	= time.getMinutes();
					second 	= time.getSeconds();
					day 	= twoDigits(day).toString();
					hour 	= twoDigits(hour).toString();
					minute 	= twoDigits(minute).toString();
					second 	= twoDigits(second).toString();
					date 	= year.concat("-", month, "-", day);
					hour 	= hour.concat(":", minute, ":", second);			
				}
				if (text !== undefined && mime == "application/json") {
					json = JSON.parse(entries[i].response.content.text);
					listValue = json.listvalue;
					if (listValue !== "[]" && listValue !== undefined && listValue !== "") {
						listValue = JSON.parse(listValue);
						listvalueLength= listValue.length;
						for (j = 0; j < listvalueLength; j++) {
							k++;
							quote[k] = listValue[j][0].split('|');
							quote[k].splice(0, 4);
							quote[k].splice(1, 7);
							quote[k].splice(3, 2);
							quote[k].splice(5, 7);
							quote[k].splice(6, 17);
							quote[k].splice(1, 0, date, hour);
						}
					}
				}
			}
		}
		quote.sort();
		for (m = 0; m <= k; m++) {
			if (m < k) {
				ticker 		=	quote[m][0];
				bid 		=	quote[m][3];
				price 		=	quote[m][4];
				vol 		=	quote[m][5];
				ask 		=	quote[m][6];
				total 		=	quote[m][7];
				next_ticker =	quote[m + 1][0];
				next_total 	=	quote[m + 1][7];
				switch (check) {
					case ( (ticker == next_ticker) && (total == next_total) ):
					case ( bid == ask || total == 0 || vol == 0 || price == 0 ):
						quote[m] = [];
						break;
				}				
			}
			quote[m] = quote[m].toString();
		}
		quote = quote.filter( function(element, index, array) {
			return quote.indexOf(element) == index;
		});
		quote.shift();
		k = quote.length;
		for (m = 0; m < k; m++) {
			subQuote[m] = quote[m].split(",");
		}
		for (n = 1; n < k; n++) {
			ticker 		= subQuote[n][0];
			bid 		= subQuote[n][3];
			price 		= subQuote[n][4];
			if (ticker == prev_ticker) {
				subQuote[n][5] 	= parseInt(subQuote[n][7]) - parseInt(subQuote[n - 1][7]);
				vol = subQuote[n][5];
			}
			else {
				vol 	= subQuote[n][5];
			}
			ask 		= subQuote[n][6];
			total 		= subQuote[n][7];
			prev_ticker = subQuote[n - 1][0];
			prev_bid 	= subQuote[n - 1][3];
			prev_ask 	= subQuote[n - 1][6];
			prev_total 	= subQuote[n - 1][7];
			switch (check) {
				case (vol == total && price > ask):
				case (price == ask):
				case (ticker == prev_ticker && price == prev_ask):
				case (price - bid > ask - price):
					subQuote[n].push('BUY');
					break;
				case (vol == total && price < bid):
				case (price == bid):
				case (ticker == prev_ticker && price == prev_bid):
				case (price - bid < ask - price):
					subQuote[n].push('SELL');
					break;
				default:
					subQuote[n].push('NA');
					break;
			}
		}
		subQuote.forEach( function(quote, index, strQuote) {
			quote.splice(3, 1);
			quote.splice(5, 1);
			quote = quote.toString();
			console.log(quote);
		}); /* */
	};
	reader.readAsText(file);
} 

function displayContents(contents) {
	var element = document.getElementById('file-content');
	element.innerHTML = contents;
}

document.getElementById('file-input')
.addEventListener('change', readSingleFile, false);
