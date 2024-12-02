<div class="modal fade" id="registerModal" tabindex="-1" aria-labelledby="registerModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0">
            <div class="modal-header bg-dark text-white py-4">
                <h5 class="modal-title" id="registerModalLabel">Create Account</h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body p-5">
                <form id="registerForm">
                    <div class="form-group">
                        <label for="signup_name">Full Name</label>
                        <input type="text" class="form-control border-dark" id="signup_name" name="name"
                            placeholder="Enter your full name" maxlength="100">
                    </div>
                    <div class="form-group">
                        <label for="signup_email">Email Address</label>
                        <input type="email" class="form-control border-dark" id="signup_email" name="email"
                            placeholder="Enter your email" maxlength="150">
                    </div>
                    <div class="form-group">
                        <label for="signup_phone_number">Phone Number</label>
                        <input type="number" class="form-control border-dark" id="signup_phone_number" name="phone_number" placeholder="Enter your phone number" oninput="javascript: if (this.value.length > this.maxLength) this.value = this.value.slice(0, this.maxLength);" maxlength="10">
                    </div>
                    <div class="form-group">
                        <label for="signup_password">Password</label>
                        <input type="password" class="form-control border-dark" id="signup_password" name="password"
                            placeholder="Enter your password" maxlength="20">
                    </div>
                    <div class="form-group">
                        <label for="signup_password_confirmation">Confirm Password</label>
                        <input type="password" class="form-control border-dark" id="signup_password_confirmation"
                            name="password_confirmation" placeholder="Confirm your password" maxlength="20">
                    </div>
                    <div class="form-group">
                        <label for="signup_role">Role</label>
                        <select class="form-control border-dark" id="signup_role" name="role">
                            <option value="" disabled selected>Select your role</option>
                            <option value="attendee">Attendee</option>
                            <option value="organizer">Organizer</option>
                        </select>
                    </div>
                    <button type="button" class="btn btn-dark btn-block mt-4" id="registerButton">Sign Up</button>
                </form>
            </div>
            <div class="modal-footer justify-content-center bg-light">
                <p class="mb-0">Already have an account? <a href="#" class="text-primary" data-toggle="modal"
                        data-target="#loginModal" data-dismiss="modal">Login</a></p>
            </div>
        </div>
    </div>
</div>
