<footer class="footer text-center text-sm-left">
    @if(session()->has('DEBUG') && env('APP_DEBUG') == true)
        <div id="debug_box" style="position: fixed; bottom: 0; left: 0; width: 100%; overflow: scroll; height: 65px; background: #eee; margin-bottom: 30px;">
            <button id="debug_close_btn" class="btn btn-outline-danger m-3 btn-sm float-right" type="button">Close</button>
            <button id="debug_expand_btn" class="btn btn-outline-warning mt-3 btn-sm float-right" type="button">Expand</button>
            <button id="debug_minimize_btn" class="btn btn-outline-warning mt-3 btn-sm float-right" type="button" style="display: none;">Minimize</button>
            <a id="debug_clear_btn" class="btn btn-outline-info mr-3 mt-3 btn-sm float-right" href="/dashboard/clear-debugger">Clear</a>
            <?php
            echo "<pre class='p-3'>";
            echo "GUID: " . session(env('GUID_SESSION_VAR')) . "<br>";
            //            print_r(session('DEBUG'));
            foreach (session()->get('DEBUG') as $s) echo $s . "<br>";
            echo "</pre>";
            ?>
        </div>

    @endif
    @foreach($roles as $role)
        @if($role->name == 'administrator')
            <i class="ti-exchange-vertical mr-2"></i>
            <a href="/dashboard/admin" class="text-muted d-none d-sm-inline-block">Switch to Admin</a>
        @endif
    @endforeach
   <span class="text-muted d-none d-sm-inline-block float-right">Crafted with <i class="mdi mdi-heart text-danger"></i> by Student Portofolio @ TBTB</span>
</footer><!--end footer-->

