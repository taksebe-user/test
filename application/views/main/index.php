<div id="symbols">
    <div style="background-color:lightgray;padding:4px 2px;">
        <button onclick='$("#contSymbol").toggle();console.log(this,$(this));$(this).text((($(this).text()==">")?"<":">"));' class='elem_name black' style='width: 23px; height: 23px; margin-left: 4px;'>></button>
        <div id="contSymbol" style="display:none">
            <ul>
                <li><span class="elem_name red blink">&nbsp;&nbsp;</span><span style='color:black;'>&nbsp;&nbsp;&ndash;&nbsp;&nbsp;аварийное состояние </span></li>
                <li><span class="elem_name green">&nbsp;&nbsp;</span><span style='color:black;'>&nbsp;&nbsp;&ndash;&nbsp;&nbsp;нормальное состояние</span></li>
                <li><span class="elem_name orange">&nbsp;&nbsp;</span><span style='color:black;'>&nbsp;&nbsp;&ndash;&nbsp;&nbsp;нет связи</span></li>
                <li><span class="elem_name black">&nbsp;&nbsp;</span><span style='color:black;'>&nbsp;&nbsp;&ndash;&nbsp;&nbsp;сигналы недоступны</span></li>
            </ul>
        </div>
    </div>
</div>

<div class="containerMain" style="width:100%;height: 100%;">
    <div id="info">
        <?= $steamShops; ?>
        <table border width="100%" id='tableCrashes' style='margin-top:10px;'>
            <tbody id='headerTable'>
                <tr style="color:white;background-color:#008CBA;font-family:Arial,sans-serif;font-style:italic;font-size:10pt;">
                    <td align="center" style="min-width:100px">
                        Возникновение аварии
                    </td>
                    <td align="center" style="min-width:160px">
                        Сообщение о месте и характере аварии
                    </td>
                    <td align="center" style="min-width:100px">
                        Подтверждение
                    </td>
                    <td align="center" style="min-width:100px">
                        Подтверждение аварии
                    </td>
                    <td align="center" style="min-width:100px">
                        Устранение аварии
                    </td>
                </tr>
            </tbody>
            <tbody id='contentTable'></tbody>
        </table>
    </div>
</div>
<div id="myModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <span class="close">&times;</span>
            <h2>Ошибка</h2>
        </div>
        <div class="modal-body">
            <p>При обновлении данных произошла ошибка.</p>
            <p>Это могло произойти по следующим причинам:</p>
            <p>&nbsp;&nbsp;&nbsp;&ndash;&nbsp;У вас отсутствует соединение с Интернетом;</p>
            <p>&nbsp;&nbsp;&nbsp;&ndash;&nbsp;Сервер перестал отвечать на запросы;</p>
            <p>&nbsp;&nbsp;&nbsp;&ndash;&nbsp;Во время передачи информации, данные были повреждены;</p>
        </div>
        <div class="modal-footer">
            <h3>Проверьте наличие Интернета. Оповестите о наличии ошибки в ИВЦ.</h3>
        </div>
    </div>

</div>
<script defer src="<?= $this->getFileHistoryDate("/public/js/form.js"); ?>"></script>
<script defer async src="<?= $this->getFileHistoryDate("/public/js/users_bottomScript.js"); ?>"></script>