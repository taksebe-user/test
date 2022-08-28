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
        <img src="/public/images/surik_look.svg" alt="403 Denied" srcset="">
    </div>
    <div class="content">
        <pre>    Ой что-то пошло не так:
 У вас не оказалось достаточных прав 
 увидеть эту страницу.
</pre>
    </div>

    <div style="display:none;">
        <?= debug_print_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 4); ?>
    </div>
</div>