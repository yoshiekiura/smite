<?php
/**
 * @var array $participantsCategory
 * @var array $statusList;
 * @var array $participantsUniv
 * @var array $univList;
 */
?>

<div class="gdlr-core-pbf-wrapper " style="padding: 115px 0px 40px 0px;">
	<div class="gdlr-core-pbf-background-wrap" style="background-color: #f3f3f3 ;"></div>
	<div class="gdlr-core-pbf-wrapper-content gdlr-core-js ">
		<div class="gdlr-core-pbf-wrapper-container clearfix gdlr-core-container">
			<div class="gdlr-core-pbf-column gdlr-core-column-60 gdlr-core-column-first">
				<div class="gdlr-core-pbf-column-content-margin gdlr-core-js ">
					<div class="gdlr-core-pbf-column-content clearfix gdlr-core-js " style="max-width: 760px ;">
						<div class="gdlr-core-pbf-element">
							<div class="gdlr-core-title-item gdlr-core-item-pdb clearfix  gdlr-core-center-align gdlr-core-title-item-caption-bottom gdlr-core-item-pdlr" style="padding-bottom: 60px ;">
								<div class="gdlr-core-title-item-title-wrap clearfix">
								<div class="gdlr-core-pbf-element">
									<div class="gdlr-core-title-item gdlr-core-item-pdb clearfix  gdlr-core-left-align gdlr-core-title-item-caption-top gdlr-core-item-pdlr">
										<div class="gdlr-core-title-item-title-wrap clearfix">
											<h3 class="gdlr-core-title-item-title gdlr-core-skin-title " style="font-size: 27px ;font-weight: 600 ;letter-spacing: 0px ;text-transform: none ;">Register As Member</h3></div>
									</div>
								</div>
								<div id="app">
									<div class="row">
										<div v-if="registered" class="col-lg-12 col-lg-offset-2">
											<div class="alert alert-success">
												<h4><i class="fa fa-info"></i> Your account has been successfully created</h4>
												<p>We have sent an email with a confirmation link to your email address. In order to complete the sign-up process, please click the confirmation link.
													If you do not receive a confirmation email, please check your spam folder. Also, please verify that you entered a valid email address in our sign-up form.
													If you need assistance, please contact us.</p>
											</div>
										</div>
										<div v-else class="col-lg-12 col-lg-offset-2">

											<div class="alert alert-info alert-dismissable alert-hotel">
												<i class="fa fa-info"></i>
												<b>Attention</b>
												Make sure the e-mail address you entered is exists and that you can open it, because we will send
												the activation code to activate your account to your e-mail.
												Before being activated, your account cannot be used yet, And please provide/upload document proof of
												your status.
											</div>
											<form id="form-register" ref="form">
												<div class="form-group row">
													<label class="col-lg-3 control-label">Email*</label>
													<div class="col-lg-5">
														<input type="text" :class="{'is-invalid': validation_error.email}" class="form-control" name="email"/>
														<div v-if="validation_error.email" class="invalid-feedback">
															{{ validation_error.email }}
														</div>
													</div>
												</div>

												<div class="form-group row">
													<label class="col-lg-3 control-label">Password*</label>
													<div class="col-lg-5">
														<input type="password" :class="{ 'is-invalid':validation_error.password }" class="form-control" name="password"/>
														<div v-if="validation_error.password" class="invalid-feedback">
															{{ validation_error.password }}
														</div>
													</div>
												</div>

												<div class="form-group row">
													<label class="col-lg-3 control-label">Confirm Password*</label>
													<div class="col-lg-5">
														<input type="password" :class="{ 'is-invalid': validation_error.confirm_password }" class="form-control" name="confirm_password"/>
														<div v-if="validation_error.confirm_password" class="invalid-feedback">
															{{ validation_error.confirm_password }}
														</div>
													</div>
												</div>
												<hr/>

												<div class="form-group row">
													<label class="col-lg-3 control-label">Your Status*</label>
													<div class="col-lg-5">
														<?= form_dropdown('status', $participantsCategory, '', [':class'=>"{'is-invalid':validation_error.status}",'v-model'=>'status_selected', 'class' => 'form-control', 'placeholder' => 'Select your status !']); ?>
														<div v-if="validation_error.status" class="invalid-feedback" >
															{{ validation_error.status }}
														</div>
													</div>
												</div>

												<div v-if="needVerification" class="form-group row">
													<label class="col-lg-3 control-label">Upload proof of your status* <small>(jpg,jpeg,png)</small></label>
													<div class="col-lg-5">
														<input type="file" name="proof" accept=".jpg,.png,.jpeg" :class="{'is-invalid':validation_error.proof}" class="form-control-file"/>
														<div v-if="validation_error.proof" class="invalid-feedback d-block">
															{{ validation_error.proof }}
														</div>
													</div>
												</div>

												<div class="form-group row">
													<label class="col-lg-3 control-label">Full Name*</label>
													<div class="col-lg-5">
														<small>*PLEASE FILL YOUR NAME CORRECTLY FOR YOUR CERTIFICATE</small>
														<input type="text" :class="{'is-invalid':validation_error.fullname}" class="form-control" name="fullname"/>
														<div v-if="validation_error.fullname" class="invalid-feedback">
															{{ validation_error.fullname }}
														</div>
													</div>
												</div>


												<div class="form-group row">
													<label class="col-lg-3 control-label">Address</label>
													<div class="col-lg-5">
														<textarea :class="{ 'is-invalid':validation_error.address }" class="form-control" name="address"></textarea>
														<div class="invalid-feedback">
															{{ validation_error.address }}
														</div>
													</div>
												</div>

												<div class="form-group row">
													<label class="col-lg-3 control-label">City</label>
													<div class="col-lg-5">
														<input type="text" :class="{'is-invalid':validation_error.city}" class="form-control" name="city"/>
														<div v-if="validation_error.city" class="invalid-feedback">
															{{ validation_error.city }}
														</div>
													</div>
												</div>

												<div class="form-group row">
													<label class="col-lg-3 control-label">Your Institution*</label>
													<div class="col-lg-5">
														<?= form_dropdown('univ', $participantsUniv, '', [':class'=>"{'is-invalid':validation_error.univ}",'v-model'=>'univ_selected', 'class' => 'form-control', 'placeholder' => 'Select your institution !']); ?>
														<div v-if="validation_error.univ" class="invalid-feedback" >
															{{ validation_error.univ }}
														</div>
													</div>
												</div>

												<div class="form-group row">
													<label class="col-lg-3 control-label">Phone/WA*</label>
													<div class="col-lg-5">
														<input type="text" :class="{ 'is-invalid':validation_error.phone}" @keypress="onlyNumber" class="form-control" name="phone"/>
														<div v-if="validation_error.phone" class="invalid-feedback">
															{{ validation_error.phone }}
														</div>

													</div>
												</div>


												<div class="form-group row">
													<label class="col-lg-3 control-label">Gender*</label>
													<div class="col-lg-5">
														<div class="radio">
															<label>
																<input type="radio" name="gender" checked value="M"/> Male
															</label>
														</div>
														<div class="radio">
															<label>
																<input type="radio" name="gender" value="F"/> Female
															</label>
														</div>
													</div>
												</div>

							<!--                    <div class="form-group row">-->
							<!--                        <label class="col-lg-3 control-label">Birthday</label>-->
							<!--                        <div class="col-lg-5">-->
							<!--                            <vuejs-datepicker :input-class="[{'is-invalid':validation_error.birthday},'form-control']"-->
							<!--                                              wrapper-class="wrapper-datepicker"-->
							<!--                                              name="birthday"></vuejs-datepicker>-->
							<!--                            <div v-if="validation_error.birthday" class="invalid-feedback d-block">-->
							<!--                                {{ validation_error.birthday }}-->
							<!--                            </div>-->
							<!--                        </div>-->
							<!--                    </div>-->

											</form>
											<hr/>
											<div class="form-group row">
												<label class="col-lg-3 control-label"></label>
												<div class="col-lg-5 col-lg-offset-3">
													<button :disabled="saving" type="button" @click="register"
															class="btn btn-outline custom-border-width btn-primary custom-border-radius font-weight-semibold text-uppercase">
														<i v-if="saving"  class="fa fa-spin fa-spinner"></i>
														Register
													</button>
													<button type="button"
															class="btn btn-outline custom-border-width btn-default custom-border-radius font-weight-semibold text-uppercase"
															id="resetBtn" style="border-color:red;color:red">Cancel
													</button>
												</div>
											</div>
										</div>
									</div>
								</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<?php $this->layout->begin_script(); ?>
<script src="<?= base_url("themes/script/sweetalert2@8.js"); ?>"></script>
<script src="<?= base_url("themes/script/vuejs-datepicker.min.js"); ?>"></script>

<script>
    var app = new Vue({
        'el': "#app",
        components: {
            vuejsDatepicker
        },
        data: {
            statusList:<?=json_encode($statusList);?>,
            status_selected:"",
            univList:<?=json_encode($statusList);?>,
            univ_selected:"",
            saving:false,
            validation_error:{},
            registered:false,
        },
		computed:{
			needVerification(){
			    var ret = false;
			    var app = this;
			    $.each(this.statusList,function (i,v) {
					if(v.id == app.status_selected){
					    ret = v.need_verify == "1";
					    return false;
					}
                });
				return ret;
			}
		},
        methods: {
            onlyNumber ($event) {
                //console.log($event.keyCode); //keyCodes value
                let keyCode = ($event.keyCode ? $event.keyCode : $event.which);
                if ((keyCode < 48 || keyCode > 57) && keyCode !== 46) { // 46 is dot
                    $event.preventDefault();
                }
            },
            register() {
                var formData = new FormData(this.$refs.form);
                // var birthday = moment(formData.get('birthday')).format("Y-MM-DD");
                var birthday = moment().format("Y-MM-DD");
                formData.set("birthday",birthday);
                this.saving = true;
                $.ajax({
                    url: '<?=base_url('member/register');?>',
                    type: 'POST',
                    contentType: false,
                    cache: false,
                    processData: false,
                    data: formData
                }).done(function (res) {
                    if(res.status == false && res.validation_error){
                        app.validation_error = res.validation_error
                    }else if(res.status == false && res.message){
                        Swal.fire('Fail',res.message,'error');
                    }else{
                        app.registered = true;
                    }
                }).fail(function (res) {
                    Swal.fire('Fail','Server fail to response !','error');
                }).always(function (res) {
                    app.saving = false;
                });
            }
        }
    });
</script>
<?php $this->layout->end_script(); ?>

