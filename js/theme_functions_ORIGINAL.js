

// LOADER DIV **************************************************************
var divLoader = '<div class="textCenter"><img src="images/loader.gif" alt="Cargando..." /></div>';
// END LOADER DIV **********************************************************

// SECONDS *****************************************************************
var now = new Date();
var seconds = now.getMinutes()+'o'+now.getSeconds();
// END SECONDS *************************************************************

function mensajeGeneral(type, text){
    var noty = $('#mensajes').noty({
        text: text,
        type: type,
        layout: 'top',
        timeout: 4000,
        dismissQueue: true
    });

    return noty;
}

function getNumeroDeNits(date1, date2){
    var d1 = date1.split("-");
    var dat1 = new Date(d1[2], parseFloat(d1[1])-1, parseFloat(d1[0]));
    var d2 = date2.split("-");
    var dat2 = new Date(d2[2], parseFloat(d2[1])-1, parseFloat(d2[0]));

    var fin = dat2.getTime() - dat1.getTime();
    var dias = Math.floor(fin / (1000 * 60 * 60 * 24))

    return dias;
}

function addTimeToDate(time,unit,objDate,dateReference){
    var dateTemp=(dateReference)?objDate:new Date(objDate);
    switch(unit){
        case 'y':
            dateTemp.setFullYear(objDate.getFullYear()+time);
            break;
        case 'M':
            dateTemp.setMonth(objDate.getMonth()+time);
            break;
        case 'w':
            dateTemp.setTime(dateTemp.getTime()+(time*7*24*60*60*1000));
            break;
        case 'd':
            dateTemp.setTime(dateTemp.getTime()+(time*24*60*60*1000));
            break;
        case 'h':
            dateTemp.setTime(dateTemp.getTime()+(time*60*60*1000));
            break;
        case 'm':
            dateTemp.setTime(dateTemp.getTime()+(time*60*1000));
            break;
        case 's':
            dateTemp.setTime(dateTemp.getTime()+(time*1000));
            break;
        default :
            dateTemp.setTime(dateTemp.getTime()+time);
            break;
    }
    return dateTemp;
}

function formatDate(date1) {
    return (date1.getDate() < 10 ? '0' : '') + date1.getDate() + '-' +
    (date1.getMonth() < 9 ? '0' : '') + (date1.getMonth()+1) + '-' +
    date1.getFullYear();
}

function fixedEncodeURIComponent (str) {
    return encodeURIComponent(str).replace(/!/g, '%21').replace(/'/g, '%27').replace(/\(/g, '%28').
    replace(/\)/g, '%29').replace(/\*/g, '%2A');
}

function reemplazar(texto,s1,s2){
    return texto.split(s1).join(s2);
}

function irA(elemID) {
    var offsetTrail = document.getElementById(elemID);
    var offsetLeft = 0;
    var offsetTop = 0;
    while (offsetTrail) {
        offsetLeft += offsetTrail.offsetLeft;
        offsetTop += offsetTrail.offsetTop;
        offsetTrail = offsetTrail.offsetParent;
    }
    if (navigator.userAgent.indexOf("Mac") != -1 && typeof document.body.leftMargin != "undefined" && navigator.appName=="Microsoft Internet Explorer" ) {
        offsetLeft += parseInt(document.body.leftMargin);
        offsetTop += parseInt(document.body.topMargin);
    }
    window.scrollTo(offsetLeft,offsetTop)
}

window.onload = function(){
    // FILTROS DATATABLE ***********************************************************
    jQuery.fn.dataTableExt.oSort['currency-asc'] = function(a,b) {
        /* Remove any commas (assumes that if present all strings will have a fixed number of d.p) */
        var x = a == "-" ? 0 : a.replace( /,/g, "" );
        var y = b == "-" ? 0 : b.replace( /,/g, "" );

        /* Remove the currency sign */
        x = x.substring( 4 );
        y = y.substring( 4 );

        /* Parse and return */
        x = parseFloat( x );
        y = parseFloat( y );
        return x - y;
    };

    jQuery.fn.dataTableExt.oSort['currency-desc'] = function(a,b) {
        /* Remove any commas (assumes that if present all strings will have a fixed number of d.p) */
        var x = a == "-" ? 0 : a.replace( /,/g, "" );
        var y = b == "-" ? 0 : b.replace( /,/g, "" );

        /* Remove the currency sign */
        x = x.substring( 4 );
        y = y.substring( 4 );

        /* Parse and return */
        x = parseFloat( x );
        y = parseFloat( y );
        return y - x;
    };

    jQuery.fn.dataTableExt.oSort['uk_date-asc']  = function(a,b) {
        var ukDatea = a.split('/');
        var ukDateb = b.split('/');

        var x = (ukDatea[2] + ukDatea[1] + ukDatea[0]) * 1;
        var y = (ukDateb[2] + ukDateb[1] + ukDateb[0]) * 1;

        return ((x < y) ? -1 : ((x > y) ?  1 : 0));
    };

    jQuery.fn.dataTableExt.oSort['uk_date-desc'] = function(a,b) {
        var ukDatea = a.split('/');
        var ukDateb = b.split('/');

        var x = (ukDatea[2] + ukDatea[1] + ukDatea[0]) * 1;
        var y = (ukDateb[2] + ukDateb[1] + ukDateb[0]) * 1;

        return ((x < y) ? 1 : ((x > y) ?  -1 : 0));
    };

    jQuery.fn.dataTableExt.oSort['slo-asc'] = function(a,b) {
        var x = (a == "-") ? 0 : a.replace( /\./g, "" ).replace( /,/, "." );
        var y = (b == "-") ? 0 : b.replace( /\./g, "" ).replace( /,/, "." );
        x = parseFloat( x );
        y = parseFloat( y );
        return ((x < y) ? -1 : ((x > y) ? 1 : 0));
    };

    jQuery.fn.dataTableExt.oSort['slo-desc'] = function(a,b) {
        var x = (a == "-") ? 0 : a.replace( /\./g, "" ).replace( /,/, "." );
        var y = (b == "-") ? 0 : b.replace( /\./g, "" ).replace( /,/, "." );
        x = parseFloat( x );
        y = parseFloat( y );
        return ((x < y) ? 1 : ((x > y) ? -1 : 0));
    };

    jQuery.fn.dataTableExt.oSort['sloComma-asc'] = function(a,b) {
        var x = (a == "-") ? 0 : a.replace( /\,/g, "" );
        var y = (b == "-") ? 0 : b.replace( /\,/g, "" );
        x = parseFloat( x );
        y = parseFloat( y );
        return ((x < y) ? -1 : ((x > y) ? 1 : 0));
    };

    jQuery.fn.dataTableExt.oSort['sloComma-desc'] = function(a,b) {
        var x = (a == "-") ? 0 : a.replace( /\,/g, "" );
        var y = (b == "-") ? 0 : b.replace( /\,/g, "" );
        x = parseFloat( x );
        y = parseFloat( y );
        return ((x < y) ? 1 : ((x > y) ? -1 : 0));
    };
    // END FILTROS DATATABLE *******************************************************
    //
    // EL DOCUMENTO ESTA TOTALMENTE CARGADO
    $(document).ready(function(){
        // ejecutamos las pestañas de todas las secciones
        $(".Tabs").idTabs();

        // close parent
        $('.close').live('click', function() {
            $(this).closest( $(this).parent().get(0).tagName ).remove();
        });

        // close parent tabs
        $('.closeTab').live('click', function() {
            $($(this).prev().attr('href')).remove();
            $(this).prev().attr('href').remove;
            $(this).closest( $(this).parent().get(0).tagName ).remove();
            $(".Tabs").idTabs();
        });
    });
}