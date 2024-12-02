<div class="modal fade" id="loginModal" tabindex="-1" aria-labelledby="loginModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0">
            <div class="modal-header bg-dark text-white py-4">
                <h5 class="modal-title " id="loginModalLabel">Welcome Back!</h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body p-5">
                <form id="loginForm">
                    <div class="form-group">
                        <label for="login_email" class=" ">Email Address</label>
                        <input type="email" class="form-control border-dark" id="login_email" name="email" 
                            placeholder="Enter your email" maxlength="150">
                    </div>
                    <div class="form-group">
                        <label for="login_password" class=" ">Password</label>
                        <input type="password" class="form-control border-dark" id="login_password" name="password" 
                            placeholder="Enter your password" maxlength="20">
                    </div>
                    <button type="button" class="btn btn-dark btn-block  mt-4">Login</button>
                </form>
            </div>
            <div class="modal-footer justify-content-center bg-light">
                <p class="mb-0">Don't have an account? <a href="#" data-toggle="modal"
                        data-target="#registerModal" data-dismiss="modal" >Sign up</a></p>
            </div>
        </div>
    </div>
</div>

