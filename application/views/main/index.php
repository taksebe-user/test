<table border=1 width=100%>
    <th>Организация</th>
    <th>Наименование</th>
    <th>Электронный вид</th>
    <?php
        foreach($uslugi as $usluga){
            echo "<tr>";
                echo "<td class={$usluga["id"]}>{$usluga["organization"]}</td>";
                echo "<td class={$usluga["id"]}>{$usluga["name"]}</td>";
                echo "<td class={$usluga["id"]}>{$usluga["has_electronic_view"]}</td>";
            echo "</tr>";
        }
        //debug($uslugi);
    ?>
</table>