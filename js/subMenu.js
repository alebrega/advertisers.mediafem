// EL DOCUMENTO ESTA TOTALMENTE CARGADO
$(document).ready(function(){
    /*
    * SUBMENUS (.submenu_right , .submenu_left)
    * Visualizar u ocultar los submenus
    */
    $('a[data-menu="on"]').click(function(e) {
        // oculto los menus mostrados anteriormente
        $('a[data-menu="on"]').each(function(){
            $('#' + $(this).attr('data-menu-name')).css('display', 'none');
        });

        var menu_element = $('#' + $(this).attr('data-menu-name'));

        var posicion = $(this).offset();

        var posicion_top = posicion.top + $(this).height() + 10;

        // obtener nombde de clase para left o right de elemento div
        if(menu_element.hasClass('submenu_right')){
            var posicion_left = posicion.left + $(this).width() - menu_element.width();
        }

        menu_element.css( 'top', posicion_top );
        menu_element.css( 'left', posicion_left );

        // muestro u oculto el menu
        if( menu_element.is(':visible') ){
            menu_element.css('display', 'none');
        }else{
            menu_element.css('display', 'table');
        }
    });
// END SUBMENU *****************************************************************
});

$(document).click(function(e) {
    // OCULTAR LOS SUBMENUS EXISTENTES
    if (!$(e.target).closest('a[data-menu="on"]').length) {
        $('a[data-menu="on"]').each(function(){
            $('#' + $(this).attr('data-menu-name')).css('display', 'none');
        });
    }
});