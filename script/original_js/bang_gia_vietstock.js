var serverVersion = 0;
var isColor = true;
var IndexCode = '';
var IndexCodeTab = '';
var CatID = 1;
var unit = 0.1;
var Commas = 2;

$(document).ready(function () {
    var html = '';
    var html_HEADER = '';
    var html_HEADER2 = '';
    if (CatID == '1') {
        html = ich.VNINDEX_Tmpl();
        html_HEADER = ich.HEADER_HSX_Tmpl();
        html_HEADER2 = ich.HEADER2_HSX_Tmpl();
        unit = 0.1;
        Commas = 2;
    }
    else if (CatID == 2) {
        html = ich.HASTCINDEX_Tmpl();
        html_HEADER = ich.HEADER_HNX_Tmpl();
        html_HEADER2 = ich.HEADER2_HNX_Tmpl();
        unit = 0.01;
        Commas = 1;
    }
    else if (CatID == 3) {
        html = ich.UPCOMINDEX_Tmpl();
        html_HEADER = ich.HEADER_UPCOM_Tmpl();  
        html_HEADER2 = ich.HEADER2_UPCOM_Tmpl();
        unit = 0.01;
        Commas = 1;
    } else if (CatID == 4) {
        html = ich.VNINDEX_Tmpl();
        html_HEADER = ich.HEADER_ETF_Tmpl();
        html_HEADER2 = ich.HEADER2_ETF_Tmpl();
        unit = 0.1;
        Commas = 2;
    }
    $("#Tab_Bottom").html(html);
    $(".watchList").html(html_HEADER);
    $(".mainDetail").html(html_HEADER2);

    if (IndexCodeTab == "VNINDEX") {
        $(".Tab_Top > ul > li > a").removeClass("Tab_Top_Selected");
        $(".Tab_Top > ul > li > a#Tab_Ho").addClass("Tab_Top_Selected");
        $(".Tab_Top > ul > li > a#Tab_Ho").removeClass("Tab_Top_UnSelected");
        $("#Note_Ho").css("display", "block");
    }
    if (IndexCodeTab == "HASTCINDEX") {
        $(".Tab_Top > ul > li > a").removeClass("Tab_Top_Selected");
        $(".Tab_Top > ul > li > a#Tab_HNX").addClass("Tab_Top_Selected");
        $(".Tab_Top > ul > li > a#Tab_HNX").removeClass("Tab_Top_UnSelected");
        $("#Note_HNX").css("display", "block");
    }
    if (IndexCodeTab == "UPCOMINDEX") {
        $(".Tab_Top > ul > li > a").removeClass("Tab_Top_Selected");
        $(".Tab_Top > ul > li > a#Tab_UP").addClass("Tab_Top_Selected");
        $(".Tab_Top > ul > li > a#Tab_UP").removeClass("Tab_Top_UnSelected");
        $("#Note_UP").css("display", "block");
    }
    if (IndexCodeTab == "VN30") {
        $(".Tab_Top > ul > li > a").removeClass("Tab_Top_Selected");
        $(".Tab_Top > ul > li > a#Tab_VN30").addClass("Tab_Top_Selected");
        $(".Tab_Top > ul > li > a#Tab_VN30").removeClass("Tab_Top_UnSelected");
        $("#Note_Ho").css("display", "block");
    }
    if (IndexCodeTab == "HNX30") {
        $(".Tab_Top > ul > li > a").removeClass("Tab_Top_Selected");
        $(".Tab_Top > ul > li > a#Tab_HNX30").addClass("Tab_Top_Selected");
        $(".Tab_Top > ul > li > a#Tab_HNX30").removeClass("Tab_Top_UnSelected");
        $("#Note_HNX").css("display", "block");
    }
    if (IndexCodeTab == "VNMID") {
        $(".Tab_Top > ul > li > a").removeClass("Tab_Top_Selected");
        $(".Tab_Top > ul > li > a#Tab_VNMID").addClass("Tab_Top_Selected");
        $(".Tab_Top > ul > li > a#Tab_VNMID").removeClass("Tab_Top_UnSelected");
        $("#Note_Ho").css("display", "block");
    }
    if (IndexCodeTab == "VN100") {
        $(".Tab_Top > ul > li > a").removeClass("Tab_Top_Selected");
        $(".Tab_Top > ul > li > a#Tab_VN100").addClass("Tab_Top_Selected");
        $(".Tab_Top > ul > li > a#Tab_VN100").removeClass("Tab_Top_UnSelected");
        $("#Note_Ho").css("display", "block");
    }
    if (IndexCodeTab == "VNSML") {
        $(".Tab_Top > ul > li > a").removeClass("Tab_Top_Selected");
        $(".Tab_Top > ul > li > a#Tab_VNSML").addClass("Tab_Top_Selected");
        $(".Tab_Top > ul > li > a#Tab_VNSML").removeClass("Tab_Top_UnSelected");
        $("#Note_Ho").css("display", "block");
    }
    if (IndexCodeTab == "VNALL") {
        $(".Tab_Top > ul > li > a").removeClass("Tab_Top_Selected");
        $(".Tab_Top > ul > li > a#Tab_VNALL").addClass("Tab_Top_Selected");
        $(".Tab_Top > ul > li > a#Tab_VNALL").removeClass("Tab_Top_UnSelected");
        $("#Note_Ho").css("display", "block");
    }
    if (IndexCodeTab == "ETF") {
        $(".Tab_Top > ul > li > a").removeClass("Tab_Top_Selected");
        $(".Tab_Top > ul > li > a#Tab_ETF").addClass("Tab_Top_Selected");
        $(".Tab_Top > ul > li > a#Tab_ETF").removeClass("Tab_Top_UnSelected");
        $("#Note_Ho").css("display", "block");
    }

    getinit();
    LoadWatchList();
    getupdateprice();
    getindex();
    setInterval("getupdateprice()", 3000);
    setInterval("getindex()", 4000);

});
function LoadWatchList() {
    var cookie_value = $.cookie(IndexCode + '_WatchList');
    if (cookie_value != null) {
        var arr = cookie_value.split("|");

        for (var i = 1; i < arr.length; i++) {
            var childRow = document.getElementById(arr[i]);
            moveRow(childRow);
        }
    }
}
function saveWatchList() {
    var list = $("#WatchList > tbody > tr");
    var cookie_value = '';
    for (var i = 0; i < list.length; i++) {
        cookie_value = cookie_value + "|" + $(list[i]).attr("id");
    }
    $.cookie(IndexCode + '_WatchList', cookie_value, { expires: 10000, path: '/' });
}
function moveRow(childRow) {
    $("#WatchList").find('tbody').append(childRow);
    childRow.setAttribute("ondblclick", "returnRow(this)");
    mysorttable("WatchList");
    saveWatchList();
}
function returnRow(childRow) {
    $("#TableDetails").find('tbody').append(childRow);
    childRow.setAttribute("ondblclick", "moveRow(this)");
    mysorttable("TableDetails");
    saveWatchList();
}

function mysorttable(tableid) {
        var sortAsc = true, // ASC or DESC sorting
        $table = $('#' + tableid),        // cache the target table DOM element
        $rows = $('tbody > tr', $table); // cache rows from target table body
        $rows.sort(function (a, b) {
            var keyA = $(a).attr("id");
            var keyB = $(b).attr("id");
            if (sortAsc) {
                return (keyA > keyB) ? 1 : 0;  // A bigger than B, sorting ascending
            } else {
                return (keyA < keyB) ? 1 : 0;  // B bigger than A, sorting descending
            }
        });
    $rows.each(function (index, row) {
        $table.append(row);                  // append rows after sort
    });
}
function getinit() {
    $.ajax({
        type: "POST",
        url: "StockHandler.ashx?option=init&getVersion=-1&IndexCode=" + IndexCode + "&catid=" + CatID,
        data: "",
        contentType: "application/json; charset=utf-8",
        dataType: "json",
        cache: false,
        async: false,
        success: function (data) {
            for (var i = 0; i < data.length; i++) {
                var row = GetRowTmpl(data[i]);
                $("#TableDetails").find('tbody').append(row);
            }
        },
        failure: function (msg) {
            alert(msg);
        }
    });
}
function GetRowTmpl(data) {
    if (CatID == 1) {
        return ich.BODY_HSX_Tmpl(data);
    }
    else if (CatID == 2) {
        return ich.BODY_HNX_Tmpl(data);
    }
    else if (CatID == 3) {
        return ich.BODY_UPCOM_Tmpl(data);
    }
    else if (CatID == 4) {
        return ich.BODY_ETF_Tmpl(data);
    }

}
function getindex() {
    $.ajax({
        type: "POST",
        url: "StockHandler.ashx?option=index&IndexCode=" + IndexCode + "&catid=" + CatID,
        contentType: "application/json",
        data: "",
        dataType: "json",
        async: false,
        timeout: 3000,
        success: function (data) {
            ParseDataIndex(data.listvalue, data.listcss);
        },
        error: function (error) {
        }
    });
}
function ParseDataIndex(listvalue, listcss) {
    var arrCss = listcss.split("|");
    var arr = listvalue.split("|");
    if (CatID == '1' || CatID == '4') {
        SetCssIndex(GetElement("IN1"), arrCss[1], arr[2]);
        SetValueCSS(GetElement("_CH1"), arrCss[1], arr[3]);
        SetValueCSS(GetElement("PC1"), arrCss[1], arr[4]);
        SetValueCSS(GetElement("KL1"), arrCss[1], AddCommas(arr[5]));
        SetValueCSS(GetElement("GT1"), arrCss[1], AddCommas(arr[6]));

        SetCssIndex(GetElement("IN2"), arrCss[2], arr[7]);
        SetValueCSS(GetElement("_CH2"), arrCss[2], arr[8]);
        SetValueCSS(GetElement("PC2"), arrCss[2], arr[9]);
        SetValueCSS(GetElement("KL2"), arrCss[2], AddCommas(arr[10]));
        SetValueCSS(GetElement("GT2"), arrCss[2], AddCommas(arr[11]));

        SetCssIndex(GetElement("IN3"), arrCss[3], arr[12]);
        SetValueCSS(GetElement("_CH3"), arrCss[3], arr[13]);
        SetValueCSS(GetElement("PC3"), arrCss[3], arr[14]);
        if (arr[15] > 0) {
            SetValueCSS(GetElement("KL3"), arrCss[3], AddCommas(parseFloat(arr[15]) + parseFloat(arr[10])));
            SetValueCSS(GetElement("GT3"), arrCss[3], AddCommas((parseFloat(arr[16]) + parseFloat(arr[11])).toFixed(2)));
        }
        else {
            SetValueCSS(GetElement("KL3"), arrCss[3], "");
            SetValueCSS(GetElement("GT3"), arrCss[3], "");
        }


    }
    else if (CatID == '2') {
        SetCssIndex(GetElement("IN2"), arrCss[2], arr[7]);
        SetValueCSS(GetElement("_CH2"), arrCss[2], arr[8]);
        SetValueCSS(GetElement("PC2"), arrCss[2], arr[9]);
        SetValueCSS(GetElement("KL2"), arrCss[2], AddCommas(arr[10]));
        SetValueCSS(GetElement("GT2"), arrCss[2], AddCommas(arr[11]));

        SetCssIndex(GetElement("IN"), arrCss[3], arr[12]);
        SetValueCSS(GetElement("_CH"), arrCss[3], arr[13]);
        SetValueCSS(GetElement("PC"), arrCss[3], arr[14]);
        SetValueCSS(GetElement("KL"), arrCss[3], AddCommas(arr[15]));
        SetValueCSS(GetElement("GT"), arrCss[3], AddCommas(arr[16]));
    }
    else {
        SetCssIndex(GetElement("INT"), arrCss[4], arr[17]);
        SetValueCSS(GetElement("_CHT"), arrCss[4], arr[18]);
        SetValueCSS(GetElement("PCT"), arrCss[4], arr[19]);
    }
    SetValueCSS(GetElement("KLT"), arrCss[4], AddCommas(parseFloat(arr[20]).toFixed(0)));
    SetValueCSS(GetElement("GTT"), arrCss[4], AddCommas(arr[21]));

    SetValueCSS(GetElement("ADV"), "u", AddCommas(arr[22]));
    SetValueCSS(GetElement("ADC"), "c", AddCommas(arr[23]));
    SetValueCSS(GetElement("DEC"), "d", AddCommas(arr[24]));
    SetValueCSS(GetElement("DEF"), "f", AddCommas(arr[25]));
    SetValueCSS(GetElement("NOC"), "p", AddCommas(arr[26]));
    SetMarketStateValue(GetElement("MKT"), arr[27]);
    SetValueCSS(GetElement("Date"), '', arr[28]);
}
function getupdateprice() {
    $.ajax({
        type: "POST",
        url: "StockHandler.ashx?option=rt&getVersion=" + serverVersion + "&IndexCode=" + IndexCode + "&catid=" + CatID,
        contentType: "application/json",
        data: "",
        dataType: "json",
        async: false,
        timeout: 3000,
        success: function (data) {
            var tmp = jQuery.parseJSON(data.listvalue);
            var tmpcss = jQuery.parseJSON(data.listcss);
            for (var i = 0; i < tmp.length; i++) {
                ParseDataInit(tmp[i][0], tmpcss[i][0]);
            }
            serverVersion = data.serverVersion;
        },
        error: function (error) {
        }
    });
}
function GetElement(ID) {
    return document.getElementById(ID);
}
function ParseDataInit(listvalue, listCss) {
    var arrcss = listCss.split("|");
    var arr = listvalue.split("|");
    var StockCode = arr[0];
    var CssB1 = arrcss[0];
    var CssB2 = arrcss[1];
    var CssB3 = arrcss[2];
    var CssBO1 = arrcss[3];
    var CssBO2 = arrcss[4];
    var CssBO3 = arrcss[5];
    var CssOP = arrcss[6];
    var CssLP = arrcss[7];
    var CssHP = arrcss[8];
    var CssLW = arrcss[9];
    var CssAV = arrcss[10];
    var CssEmty = '';
    var cell = document.getElementById(arr[4] + "_ST");
    if (cell != null) {
        SetCSS(cell, CssLP, arr[4]);
        SetCssStock(GetElement(arr[4] + "_ST2"), CssLP, arr[4]);
    }
    else {
        return;
    }
    if (serverVersion <= 0) {
        //    SetCssValueNoBg(GetElement(arr[0] + "_FL"), CssLP, arr[1]); //OpenPrice
        //    SetCssValueNoBg(GetElement(arr[0] + "_CE"), CssLP, arr[1]); //OpenPrice
        //    SetCssValueNoBg(GetElement(arr[0] + "_PR"), CssLP, arr[1]); //OpenPrice
        //SetCssValueNoBg(GetElement(arr[4] + "_VolBuy"), CssEmty, formatvol(arr[5])); //VolBuy
        SetCssValueNoBg(GetElement(arr[4] + "_BV4"), CssEmty, formatstockvol(arr[6])); //BV3
        SetCssValueNoBg(GetElement(arr[4] + "_BV3"), CssB3, formatstockvol(arr[7])); //BV3
        SetCssValueNoBg(GetElement(arr[4] + "_BV2"), CssB2, formatstockvol(arr[9])); //BV2
        SetCssValueNoBg(GetElement(arr[4] + "_BOV2"), CssBO2, formatstockvol(arr[20])); //BOV2
        SetCssValueNoBg(GetElement(arr[4] + "_BOV3"), CssBO3, formatstockvol(arr[22])); //BOV3
        SetCssValueNoBg(GetElement(arr[4] + "_BOV4"), CssEmty, formatstockvol(arr[23])); //BOV3
        if (arr[12] == "ATO" || arr[12] == "ATC") {
            SetCssValueNoBg(GetElement(arr[4] + "_B1"), CssEmty, arr[12]); //B1
            SetCssValueNoBg(GetElement(arr[4] + "_BV1"), CssEmty, formatstockvol(arr[11])); //BV1
        }
        else {
            SetCssValueNoBg(GetElement(arr[4] + "_B1"), CssB1, arr[12]); //B1
            SetCssValueNoBg(GetElement(arr[4] + "_BV1"), CssB1, formatstockvol(arr[11])); //BV1
        }
        if (arr[17] == "ATO" || arr[17] == "ATC") {
            SetCssValueNoBg(GetElement(arr[4] + "_BOV1"), CssEmty, formatstockvol(arr[18])); //BOV1
            SetCssValueNoBg(GetElement(arr[4] + "_BO1"), CssEmty, arr[17]); //BO1
        }
        else {
            SetCssValueNoBg(GetElement(arr[4] + "_BOV1"), CssBO1, formatstockvol(arr[18])); //BOV1
            SetCssValueNoBg(GetElement(arr[4] + "_BO1"), CssBO1, arr[17]); //BO1
        }
        SetCssValueNoBg(GetElement(arr[4] + "_B3"), CssB3, arr[8]); //B3
        SetCssValueNoBg(GetElement(arr[4] + "_B2"), CssB2, arr[10]); //B2
        SetCssValueNoBg(GetElement(arr[4] + "_LP"), CssLP, arr[13]); //LastPrice
        SetCssValueNoBg(GetElement(arr[4] + "_CH"), CssLP, arr[14]); //Change
        SetCssValueNoBg(GetElement(arr[4] + "_LV"), CssLP, formatvol(arr[16])); //LastVol
        SetCssValueNoBg(GetElement(arr[4] + "_BO2"), CssBO2, arr[19]); //BO2
        SetCssValueNoBg(GetElement(arr[4] + "_BO3"), CssBO3, arr[21]); //BO3
        //SetCssValueNoBg(GetElement(arr[4] + "_VolSell"), CssEmty, formatvol(arr[24])); //VolSell
        SetCssValueNoBg(GetElement(arr[4] + "_TV"), CssLP, formatstockvol(arr[25])); //TotalVol
        SetCssValueNoBg(GetElement(arr[4] + "_FBV"), CssEmty, formatvol(arr[27])); //NN mua
        //SetCssValueNoBg(GetElement(arr[4] + "_FSV"), CssEmty, formatvol(arr[28])); //NN ban1
        //SetCssValueNoBg(GetElement(arr[4] + "_Room"), CssEmty, formatvol(arr[29])); //OpenPrice
        //SetCssValueNoBg(GetElement(arr[4] + "_PE"), CssEmty, arr[30]); //OpenPrice
        //SetCssValueNoBg(GetElement(arr[4] + "_EPS"), CssEmty, formatvol(arr[31])); //OpenPrice
        SetCssValueNoBg(GetElement(arr[4] + "_OP"), CssOP, arr[32]); //OpenPrice
        SetCssValueNoBg(GetElement(arr[4] + "_HP"), CssLP, arr[34]); //High
        SetCssValueNoBg(GetElement(arr[4] + "_LW"), CssLW, arr[35]); //Low
        SetCssValueNoBg(GetElement(arr[4] + "_AV"), CssAV, arr[33]); //Avg
        SetValueCSS(GetElement(arr[4] + "_iNav"), CssAV, formatvol2(arr[41] / 10)); //Avg
        SetValueCSS(GetElement(arr[4] + "_iIndex"), CssAV, formatvol2(arr[42] / 10)); //Avg
    }
    else {
        //SetValueCSS(GetElement(arr[4] + "_VolBuy"), CssEmty, formatvol(arr[5])); //VolBuy
        SetValueCSS(GetElement(arr[4] + "_BV3"), CssB3, formatstockvol(arr[7])); //BV3
        SetValueCSS(GetElement(arr[4] + "_BV2"), CssB2, formatstockvol(arr[9])); //BV2
        SetValueCSS(GetElement(arr[4] + "_BOV2"), CssBO2, formatstockvol(arr[20])); //BOV2
        SetValueCSS(GetElement(arr[4] + "_BOV3"), CssBO3, formatstockvol(arr[22])); //BOV3

        if (arr[12] == "ATO" || arr[12] == "ATC") {
            SetValueCSS(GetElement(arr[4] + "_B1"), CssEmty, arr[12]); //B1
            SetValueCSS(GetElement(arr[4] + "_BV1"), CssEmty, formatstockvol(arr[11])); //BV1
        }
        else {
            SetValueCSS(GetElement(arr[4] + "_B1"), CssB1, arr[12]); //B1
            SetValueCSS(GetElement(arr[4] + "_BV1"), CssB1, formatstockvol(arr[11])); //BV1
        }
        if (arr[17] == "ATO" || arr[17] == "ATC") {
            SetValueCSS(GetElement(arr[4] + "_BOV1"), CssEmty, formatstockvol(arr[18])); //BOV1
            SetValueCSS(GetElement(arr[4] + "_BO1"), CssEmty, arr[17]); //BO1
        }
        else {
            SetValueCSS(GetElement(arr[4] + "_BOV1"), CssBO1, formatstockvol(arr[18])); //BOV1
            SetValueCSS(GetElement(arr[4] + "_BO1"), CssBO1, arr[17]); //BO1
        }

        SetValueCSS(GetElement(arr[4] + "_B3"), CssB3, arr[8]); //B3
        SetValueCSS(GetElement(arr[4] + "_B2"), CssB2, arr[10]); //B2
        SetValueCSS(GetElement(arr[4] + "_LP"), CssLP, arr[13]); //LastPrice
        SetValueCSS(GetElement(arr[4] + "_CH"), CssLP, arr[14]); //Change
        SetValueCSS(GetElement(arr[4] + "_LV"), CssLP, formatstockvol(arr[16])); //LastVol
        SetValueCSS(GetElement(arr[4] + "_BO2"), CssBO2, arr[19]); //BO2
        SetValueCSS(GetElement(arr[4] + "_BO3"), CssBO3, arr[21]); //BO3
        //SetValueCSS(GetElement(arr[4] + "_VolSell"), CssEmty, formatvol(arr[24])); //VolSell
        SetValueCSS(GetElement(arr[4] + "_TV"), CssLP, formatstockvol(arr[25])); //TotalVol
        SetValueCSS(GetElement(arr[4] + "_FBV"), CssEmty, formatvol(arr[27])); //NN mua
        //SetValueCSS(GetElement(arr[4] + "_FSV"), CssEmty, formatvol(arr[28])); //NN ban1
        //SetValueCSS(GetElement(arr[4] + "_Room"), CssEmty, formatvol(arr[29])); //OpenPrice
        //SetValueCSS(GetElement(arr[4] + "_PE"), CssEmty, arr[30]); //OpenPrice
        //SetValueCSS(GetElement(arr[4] + "_EPS"), CssEmty, AddCommas(arr[31])); //OpenPrice
        SetValueCSS(GetElement(arr[4] + "_OP"), CssOP, arr[32]); //OpenPrice
        SetValueCSS(GetElement(arr[4] + "_HP"), CssLP, arr[34]); //High
        SetValueCSS(GetElement(arr[4] + "_LW"), CssLW, arr[35]); //Low
        SetValueCSS(GetElement(arr[4] + "_AV"), CssAV, arr[33]); //Avg
        SetValueCSS(GetElement(arr[4] + "_iNav"), CssAV, formatvol2(arr[41] / 10)); //Avg
        SetValueCSS(GetElement(arr[4] + "_iIndex"), CssAV, formatvol2(arr[42] / 10)); //Avg

    }
}
function AddCommas(data) {
    var nStr = data;
    nStr += '';
    x = nStr.split('.');
    x1 = x[0];
    x2 = x.length > 1 ? '.' + x[1] : '';
    var rgx = /(\d+)(\d{3})/;
    while (rgx.test(x1)) {
        x1 = x1.replace(rgx, '$1' + ',' + '$2');
    }
    return x1 + x2;
}
function SetCssStock(cell, _css, val) {
    if (_css == 'u' || _css == 'c') {
        cell.innerHTML = "&nbsp;" + val + ' ▲';
    }
    else if (_css == 'd' || _css == 'f') {
        cell.innerHTML = "&nbsp;" + val + ' ▼';
    }
    else {
        cell.innerHTML = "&nbsp;" + val + ' ■';
    }
    cell.className = _css;
}
function SetCssIndex(cell, _css, val) {
    if (val == "0" || val == "0.0" || val == "0.00") {
        val = "";
        //return;
    }
    if (_css == 'u') {
        cell.innerHTML = val + ' ▲ ';
    }
    else if (_css == 'd') {
        cell.innerHTML = val + ' ▼ ';
    }
    else {
        cell.innerHTML = val + ' ■ ';
    }
    cell.className = _css;
}
function SetValueCSS(cell, css, val) {
    if (val == "0" || val == "0.0" || val == "0.00")
        val = "";
    if (cell != null && (cell.innerHTML.trim() != val || cell.className != css)) {
        cell.className = "bg3 " + css;
        setTimeout(function () { SetValue(cell, val); }, 500);
        setTimeout(function () { SetCSS(cell, css); }, 1000);
    }

}

function SetCSS(cell, css) {
    cell.className = css;
}
function SetValue(cell, val) {
    cell.className = "bg3";
    cell.innerHTML = val;
}
function SetMarketStateValue(cell, val) {
    var result = "Thị trường đóng cửa";
    if (val == "O") result = "Khớp lệnh liên tục";
    if (val == "P") result = "Khớp lệnh định kỳ mở của";
    if (val == "A") result = "Khớp lệnh định kỳ đóng của";
    if (val == "I") result = "Tạm nghỉ giữa phiên";
    if (val == "C") result = "Giao dịch thỏa thuận";
    if (val == "5") result = "Khớp lệnh liên tục";
    if (val == "30") result = "Khớp lệnh định kỳ đóng của";
    if (val == "35") result = "Giao dịch thỏa thuận";
    if (val == "10") result = "Tạm nghỉ giữa phiên";
    if (parseInt(val) == "5") result = "Khớp lệnh liên tục";
    if (val == "30") result = "Khớp lệnh định kỳ đóng của";
    if (val == "35") result = "Giao dịch thỏa thuận";
    if (val == "10") result = "Tạm nghỉ giữa phiên";
    cell.innerHTML = result;
}
//gan du lieu lan dau
function SetCssValueNoBg(cell, css, val) {
    if (val == "0" || val == "0.0" || val == "0.00")
        val = "";
    if (cell != null) {
        cell.innerHTML = val;
        cell.className = css;
    }
}
//gan du lieu index
function SetValueBg(cell, css, val) {
    if (cell.innerHTML.trim() != val) {
        var csstmp = cell.className;
        cell.className = "bg3 " + css;
        setTimeout(function () { SetValue(cell, val); }, 500);
        setTimeout(function () { SetCSS(cell, csstmp); }, 1000);
    }
}
function formatvol(_value) {
    return AddCommas(parseFloat(_value) * unit);
}
function formatvol2(_value) {
    return AddCommas((parseFloat(_value) * unit).toFixed(2));
}
function formatstockvol(_value) {
    return AddCommas(AddCommasToLast((parseFloat(_value) * unit).toFixed(0).toString(), Commas));
}
String.prototype.insert = function (index, string) {
    if (index > 0)
        return this.substring(0, index) + string + this.substring(index, this.length);
    else
        return string + this;
};
function AddCommasToLast(last, dec) {
    var val = last;
    if (last.length > Commas)
        val = last.insert(last.length - dec, ",");
    return val;
}
function sortWatchList(index, option) {
    $("#WatchList").tablesorter({ sortList: [[index, option]] });
}
function sortTableDetails(index, option) {
    $("#TableDetails").tablesorter({ sortList: [[index, option]] });
}