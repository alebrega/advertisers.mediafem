<div id="page">
    <table style="width:100%;text-align:right;">
        <tr>
            <?php
            if (isset($this->nombre_usuario)) {
                ?>
                <td style="color: black;font-weight: bold;">
                    <?= $this->nombre_usuario ?>
                </td>
                <?php
            }
            ?>
            <td style="width: 70px;"><a href="/auth/logout">Cerrar Sesion</a></td>
        </tr>
    </table>
    <div id="navigation">

        <div id="nav">
            <ul>
                <li style="margin-right: 50px;"><a class="nohover" href="/"><img alt="MediaFem" src="/images/mediafem-blanco.png"></a></li>
                <li class="page_item page-item-1"><a title="Campa&ntilde;as" href="/">Campa&ntilde;as</a></li>
                <!--
                <li class="page_item page-item-7"><a title="Pagos" href="/payment">Opcion 3</a></li>
                <li class="page_item page-item-9"><a title="Mi Cuenta" href="/micuenta">Opcion 4</a></li>
                -->
                <li style="float: right;background-color: #F7A8CB;" class="page_item page-item-9">
                    <a target="_blank" title="Ayuda" href="http://ayuda.mediafem.com">Ayuda</a>
                </li>
            </ul>
        </div>

    </div>
