<style>
    .wrapper {
        width: 100%;
        height: 100%;
        position: fixed;
        top: 50px;
        left: 0;
        display: flex;
        align-items: center;
        align-content: center;
        justify-content: space-around;
        overflow: auto;
    }

    .img.block {
        width: 35%;
    }

    .img.block img {
        display: block;
        border: none;
        width: 100%;
    }
</style>
<div class="wrapper">
    <div class="img block">
        <img src="/public/images/surik.svg" alt="Not Found" srcset="">
    </div>
    <div class="content">
        <pre>Что-то пошло не так:
Запрошенная страница не найдена!

Перейти на <a href="/">Главную</a>
</pre>
    </div>

    <div style="display:none;">
        <?= debug_print_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 4); ?>
    </div>
</div>