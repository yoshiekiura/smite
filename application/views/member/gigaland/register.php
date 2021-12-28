<?php

/**
 * @var array $participantsCategory
 * @var array $statusList;
 * @var array $participantsUniv
 * @var array $univList;
 */
$this->layout->begin_head();

/**
 * @var $content
 */
$theme_path = base_url("themes/gigaland") . "/";
?>
<link href="<?= base_url(); ?>themes/script/chosen/chosen.css" rel="stylesheet">
<?php $this->layout->end_head(); ?>
<section id="subheader" class="text-light" data-bgimage="url(<?= $theme_path ?>/images/background/subheader.jpg) top">
    <div class="center-y relative text-center" style="background-size: cover;">
        <div class="container" style="background-size: cover;">
            <div class="row" style="background-size: cover;">

                <div class="col-md-12 text-center" style="background-size: cover;">
                    <h1>Registrasi Akun</h1>
                </div>
                <div class="clearfix" style="background-size: cover;"></div>
            </div>
        </div>
    </div>

    <!-- <div class="container">
        <div class="row">
            <div class="col-md-8 order-2 order-md-1 align-self-center p-static">
                <h1 class="text-color-dark font-weight-bold">Registrasi Akun</h1>
            </div>
            <div class="col-md-4 order-1 order-md-2 align-self-center">
                <ul class="breadcrumb d-block text-md-right breadcrumb-dark">
                    <li><a href="?= base_url('site/home'); ?>" class="text-color-dark">Beranda</a></li>
                    <li class="active">Registrasi</li>
                </ul>
            </div>
        </div>
    </div> -->
</section>

<section id="app" class="custom-section-padding">
    <div class="container">
        <div class="row">
            <!-- NOTE Setelah Submmit -->
            <div v-if="page == 'registered'" class="col-lg-12 col-lg-offset-2">
                <div class="alert alert-success">
                    <h4><i class="fa fa-info"></i> Akunmu berhasil dibuat</h4>
                    <p>Kami telah mengirim link konfirmasi ke alamat emailmu. Untuk melengkapi proses registrasi, Silahkan klik <i>confirmation link</i>.
                        Jika tidak menerima email konfirmasi, silakan cek folder spam. Kemudian, mohon pastikan anda memasukan alamat email yg valid saat mengisi form pendaftaran. Jika perlu bantuan, silakan kontak kami.</p>
                </div>

                <div class="card mt-2">
                    <div class="card-header text-center">
                        <h4 class="m-0 p-0"><strong class="font-weight-extra-bold">Halaman untuk mengonfirmasi riwayat penagihan dan invoice display</strong></h4>
                    </div>
                    <div class="card-body">
                        <table class="table">
                            <thead>
                                <th></th>
                                <th>Event Name</th>
                                <th>Pricing</th>
                            </thead>
                            <tbody>
                                <tr v-for="item in transactionsSort">
                                    <td></td>
                                    <td>{{ item.product_name}}</td>
                                    <td>{{ formatCurrency(item.price) }}</td>
                                </tr>
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td></td>
                                    <td class="text-right font-weight-bold">Total :</td>
                                    <td>{{ formatCurrency(totalPrice) }}</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>

                <div class="col-sm-4 mt-2" v-for="account in paymentBank">
                    <div class="card">
                        <div class="card-body">
                            <h3 class="card-title">{{ account.bank }}</h3>
                            <p class="card-text">
                            <table>
                                <tr>
                                    <th>Account Number</th>
                                    <td>:</td>
                                    <td>{{ account.no_rekening }}</td>
                                </tr>
                                <tr>
                                    <th>Account Holder</th>
                                    <td>:</td>
                                    <td>{{ account.holder }}</td>
                                </tr>
                            </table>
                            </p>
                        </div>
                    </div>
                </div>

                <div class="alert alert-success mt-2">
                    <h4><i class="fa fa-info"></i> Konfirmasi Pembayaran</h4>
                    <p><strong>Untuk melakukan konfirmasi pembayaran silakan login, kemudian akses menu "Keranjang dan Pembayaran"</strong></p>
                </div>
            </div>


            <!-- NOTE Payment -->
            <div v-if="page == 'payment'" class="col-lg-12 col-lg-offset-2">

                <div class="card mt-2">
                    <div class="card-header text-center">
                        <h4 class="m-0 p-0"><strong class="font-weight-extra-bold">Data Akun</strong></h4>
                    </div>
                    <div class="card-body">
                        <table class="table">
                            <tbody>
                                <tr>
                                    <td>Email</td>
                                    <td>:</td>
                                    <th>{{data.email}}</th>
                                </tr>
                                <tr>
                                    <td>Nama</td>
                                    <td>:</td>
                                    <th>{{data.fullname}}</th>
                                </tr>
                                <tr>
                                    <td>Member ID</td>
                                    <td>:</td>
                                    <th>{{data.id}}</th>
                                </tr>
                                <tr>
                                    <td>Invoince ID</td>
                                    <td>:</td>
                                    <th>{{data.id_invoice}}</th>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="card mt-2">
                    <div class="card-header text-center">
                        <h4 class="m-0 p-0"><strong class="font-weight-extra-bold">Event</strong></h4>
                    </div>
                    <div class="card-body">
                        <table class="table">
                            <thead>
                                <th></th>
                                <th>Event Name</th>
                                <th>Pricing</th>
                            </thead>
                            <tbody>
                                <tr v-for="item in transactionsSort">
                                    <td></td>
                                    <td>{{ item.product_name}}</td>
                                    <td>{{ formatCurrency(item.price) }}</td>
                                </tr>
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td></td>
                                    <td class="text-right font-weight-bold">Total :</td>
                                    <td>{{ formatCurrency(totalPrice) }}</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>

                <div class="form-group mb-2">
                    <select name="selectedPaymentMethod" id="selectedPaymentMethod" :class="{ 'is-invalid':validation_error.selectedPaymentMethod}" class="form-control selectedPaymentMethod mt-2 text-center" v-model="selectedPaymentMethod">
                        <option v-for="(method,ind) in paymentMethod" :value="method.key">{{method.desc}}</option>
                    </select>
                    <div v-if="validation_error.selectedPaymentMethod" class="invalid-feedback">
                        {{ validation_error.selectedPaymentMethod }}
                    </div>
                </div>
                <hr />
                <div class="form-group row mb-2 mb-5">
                    <div class="col-lg-12 text-center">
                        <button :disabled="saving" type="button" @click="checkout" class="btn btn-primary custom-border-radius font-weight-semibold text-uppercase">
                            <i v-if="saving" class="fa fa-spin fa-spinner"></i>
                            Submit
                        </button>
                    </div>
                </div>

            </div>

            <!-- NOTE Sebelum Submit -->
            <div v-if="page == 'register'" class="col-lg-12 col-lg-offset-2">
                <div class="alert alert-info alert-dismissable alert-hotel mt-5">
                    <i class="fa fa-info"></i>
                    <b>Perhatian</b>
                    Pastikan alamat email yang dimasukkan valid dan dapat anda akses, karena kami akan mengirimkan kode aktivasi melalui email tersebut. Akun anda tidak dapat digunakan sebelum diaktivasi terlebih dahulu.
                </div>
                <form id="form-register" ref="form">
                    <div class="form-group row mb-2">
                        <label class="col-lg-3 control-label control-label-bold">Email*</label>
                        <div class="col-lg-9">
                            <input type="text" :class="{'is-invalid': validation_error.email}" class="form-control" name="email" value="bawaihi@ulm.ac.id" />
                            <div v-if="validation_error.email" class="invalid-feedback">
                                {{ validation_error.email }}
                            </div>
                        </div>
                    </div>

                    <div class="form-group row mb-2">
                        <label class="col-lg-3 control-label">Password*</label>
                        <div class="col-lg-9">
                            <input type="password" :class="{ 'is-invalid':validation_error.password }" class="form-control" name="password" value="b" />
                            <div v-if="validation_error.password" class="invalid-feedback">
                                {{ validation_error.password }}
                            </div>
                        </div>
                    </div>

                    <div class="form-group row mb-2">
                        <label class="col-lg-3 control-label">Confirm Password*</label>
                        <div class="col-lg-9">
                            <input type="password" :class="{ 'is-invalid': validation_error.confirm_password }" class="form-control" name="confirm_password" value="b" />
                            <div v-if="validation_error.confirm_password" class="invalid-feedback">
                                {{ validation_error.confirm_password }}
                            </div>
                        </div>
                    </div>
                    <hr />

                    <div class="form-group row mb-2">
                        <label class="col-lg-3 control-label">Status*</label>
                        <div class="col-lg-9">
                            <?= form_dropdown('status', $participantsCategory, '', [':class' => "{'is-invalid':validation_error.status}", 'id' => 'status', 'v-model' => 'status_selected', 'class' => 'form-control', 'placeholder' => 'Select your status !']); ?>
                            <div v-if="validation_error.status" class="invalid-feedback">
                                {{ validation_error.status }}
                            </div>
                        </div>
                    </div>

                    <div v-if="needVerification" class="form-group row mb-2">
                        <label class="col-lg-3 control-label">Mohon unggah bukti identitas anda* <small>(jpg,jpeg,png)</small></label>
                        <div class="col-lg-9">
                            <input type="file" name="proof" accept=".jpg,.png,.jpeg" :class="{'is-invalid':validation_error.proof}" class="form-control-file" />
                            <div v-if="validation_error.proof" class="invalid-feedback d-block">
                                {{ validation_error.proof }}
                            </div>
                        </div>
                    </div>

                    <div class="form-group row mb-2">
                        <label class="col-lg-3 control-label">Nama Lengkap*</label>
                        <div class="col-lg-9">
                            <small>*Mohon mengisi nama dengan lengkap dan benar (beserta gelar) untuk sertifikat</small>
                            <input type="text" :class="{'is-invalid':validation_error.fullname}" class="form-control" name="fullname" value="Muhammad Bawaihi" />
                            <div v-if="validation_error.fullname" class="invalid-feedback">
                                {{ validation_error.fullname }}
                            </div>
                        </div>
                    </div>


                    <div class="form-group row mb-2">
                        <label class="col-lg-3 control-label">Alamat</label>
                        <div class="col-lg-9">
                            <textarea :class="{ 'is-invalid':validation_error.address }" class="form-control" name="address">-</textarea>
                            <div class="invalid-feedback">
                                {{ validation_error.address }}
                            </div>
                        </div>
                    </div>

                    <div class="form-group row mb-2">
                        <label class="col-lg-3 control-label">Kota</label>
                        <div class="col-lg-9">
                            <input type="text" :class="{'is-invalid':validation_error.city}" class="form-control" name="city" value="Kota" />
                            <div v-if="validation_error.city" class="invalid-feedback">
                                {{ validation_error.city }}
                            </div>
                        </div>
                    </div>

                    <div class="form-group row mb-2">
                        <label class="col-lg-3 control-label">Institusi*</label>
                        <div class="col-lg-9">
                            <?= form_dropdown('univ', $participantsUniv, '', [':class' => "{'is-invalid':validation_error.univ}", 'v-model' => 'univ_selected', 'class' => 'form-control chosen', 'placeholder' => 'Select your institution !']); ?>
                            <div v-if="validation_error.univ" class="invalid-feedback">
                                {{ validation_error.univ }}
                            </div>
                        </div>
                    </div>

                    <div v-if="univ_selected == <?= Univ_m::UNIV_OTHER; ?>" class="form-group row mb-2">
                        <label class="col-lg-3 control-label">Institusi lain</label>
                        <div class="col-lg-9">
                            <input type="text" :class="{ 'is-invalid':validation_error.other_institution}" class="form-control" name="other_institution" />
                            <div v-if="validation_error.phone" class="invalid-feedback">
                                {{ validation_error.other_institution }}
                            </div>

                        </div>
                    </div>


                    <div class="form-group row mb-2">
                        <label class="col-lg-3 control-label">No HP/WA*</label>
                        <div class="col-lg-9">
                            <input type="text" :class="{ 'is-invalid':validation_error.phone}" @keypress="onlyNumber" class="form-control" name="phone" value="082153606887" />
                            <div v-if="validation_error.phone" class="invalid-feedback">
                                {{ validation_error.phone }}
                            </div>

                        </div>
                    </div>


                    <div class="form-group row mb-2">
                        <label class="col-lg-3 control-label">Jenis Kelamin*</label>
                        <div class="col-lg-9">
                            <div class="radio">
                                <label>
                                    <input type="radio" name="gender" checked value="M" /> Laki-laki
                                </label>
                            </div>
                            <div class="radio">
                                <label>
                                    <input type="radio" name="gender" value="F" /> Wanita
                                </label>
                            </div>
                        </div>
                    </div>

                    <div class="form-group row mb-2">
                        <label class="col-lg-3 control-label">Sponsor</label>
                        <div class="col-lg-9">
                            <input type="text" :class="{'is-invalid':validation_error.sponsor}" class="form-control" name="sponsor" value="-" />
                            <div v-if="validation_error.sponsor" class="invalid-feedback">
                                {{ validation_error.sponsor }}
                            </div>
                        </div>
                    </div>

                    <!-- NOTE Events -->
                    <div class="col-lg-12" v-if="status_selected">
                        <hr />
                        <div class="card">
                            <div class="card-header text-center">
                                <h2 class="m-0 p-0"><strong class="font-weight-extra-bold">Acara</strong></h2>
                            </div>
                            <div class="card-body text-center">
                                Silakan pilih acara yang Anda inginkan. *Acara tersedia berdasarkan status dan tanggal Anda
                            </div>
                        </div>
                        <!-- <div class="row">
                            <div class="col-md-9">
                                <div class="overflow-hidden mb-1">
                                    <h2 class="font-weight-normal text-7 mb-0"><strong class="font-weight-extra-bold">Acara</strong></h2>
                                </div>
                                <div class="overflow-hidden mb-4 pb-3">
                                    <p class="mb-0"></p>
                                </div>
                            </div>
                            <div class="col-md-3"></div>
                        </div> -->

                        <div class="row">
                            <div class="accordion accordion-quaternary col-md-12">
                                <div v-for="(event, index) in filteredEvent" class="card card-default mt-2" v-bind:key="index">
                                    <div class="card-header">
                                        <h4 class="card-title m-0">
                                            <a class="accordion-toggle" data-toggle="collapse" :href="'#accordion-'+index" aria-expanded="true">
                                                {{ event.name }}
                                            </a>
                                        </h4>
                                    </div>
                                    <div :id="'accordion-'+index" class="collapse show table-responsive">
                                        <div>
                                            <div v-if="event.participant >= event.kouta" class="alert alert-warning text-center">
                                                <h4>Maaf Kouta untuk acara ini penuh</h4>
                                            </div>
                                            <table class="table">
                                                <thead>
                                                    <tr>
                                                        <th>Kategori</th>
                                                        <th v-for="pricing in event.pricingName" class="text-center"><span v-html="pricing.title"></span></th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr v-for="member in event.memberStatus">
                                                        <td>{{ member }}</td>
                                                        <td v-for="pricing in event.pricingName" class="text-center">
                                                            <span v-if="pricing.pricing[member]">
                                                                {{ formatCurrency(pricing.pricing[member].price) }}
                                                                <div v-if="member == status_text" class="de-switch mt-2" style="background-size: cover;">
                                                                    <input type="checkbox" :id="`switch-unlock_${member}_${event.name}`" :value="pricing.pricing[member].added" class="checkbox" v-model="pricing.pricing[member].added" @click="addEvent($event,pricing.pricing[member],member,event.name)">
                                                                    <label :for="`switch-unlock_${member}_${event.name}`"></label>
                                                                </div>
                                                                <div v-else>
                                                                    <button type="button" v-if="member != status_text" style="cursor:not-allowed;color:#fff;" aria-disabled="true" disabled class="btn btn-sm btn-danger">Not Available</button>
                                                                </div>
                                                                <!-- <button type="button" @click="addEvent(pricing.pricing[member],member,event.name)" v-if="member == status_text" :disabled="pricing.pricing[member].added" class="btn btn-sm btn-warning">Add Event</button> -->
                                                            </span>
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                                <div class="card card-default mt-2">
                                    <div class="card-header text-center">
                                        <b>{{ formatCurrency(total) }}</b>
                                    </div>
                                </div>
                                <div v-if="validation_error.eventAdded" style="font-size: .875em;color: #dc3545;">
                                    {{ validation_error.eventAdded }}
                                </div>

                                <!-- <div class="form-group mb-2">
                                    <select name="selectedPaymentMethod" id="selectedPaymentMethod" :class="{ 'is-invalid':validation_error.selectedPaymentMethod}" class="form-control selectedPaymentMethod mt-2 text-center" v-model="selectedPaymentMethod">
                                        <option v-for="(method,ind) in paymentMethod" :value="method.key">{{method.desc}}</option>
                                    </select>
                                    <div v-if="validation_error.selectedPaymentMethod" class="invalid-feedback">
                                        {{ validation_error.selectedPaymentMethod }}
                                    </div>
                                </div> -->
                            </div>
                        </div>
                    </div>
                    <!-- NOTE End Events -->

                </form>
                <hr />
                <div class="form-group row mb-2 mb-5">
                    <div class="col-lg-12 text-center">
                        <button :disabled="saving" type="button" @click="register" class="btn btn-primary custom-border-radius font-weight-semibold text-uppercase">
                            <i v-if="saving" class="fa fa-spin fa-spinner"></i>
                            Next
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal" id="modal-select-payment">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title">Select Payment Method</h4>
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                    </div>
                    <div class="modal-body">
                        <iframe id="sgoplus-iframe" style="width:100%"></iframe>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<?php $this->layout->begin_script(); ?>
<script src="<?= base_url("themes/script/sweetalert2@8.js"); ?>"></script>
<script src="<?= base_url("themes/script/vuejs-datepicker.min.js"); ?>"></script>
<script src="<?= base_url("themes/script/chosen/chosen.jquery.min.js"); ?>"></script>

<?php if (isset(Settings_m::getEspay()['jsKitUrl'])) : ?>
    <script src="<?= Settings_m::getEspay()['jsKitUrl']; ?>"></script>
<?php endif; ?>
<script>
    var app = new Vue({
        'el': "#app",
        components: {
            vuejsDatepicker
        },
        data: {
            statusList: <?= json_encode($statusList); ?>,
            status_selected: "",
            status_text: "",
            univList: <?= json_encode($statusList); ?>,
            univ_selected: "",
            saving: false,
            validation_error: {},
            page: 'register',

            paymentMethod: [],
            selectedPaymentMethod: '',
            events: <?= json_encode($events) ?>,
            eventAdded: [],
            adding: false,
            transactions: null,
            paymentBank: null,

            data: {},
        },
        mounted: function() {

            // NOTE Set Payment Method
            let paymentData = <?= json_encode($paymentMethod) ?>;
            let tempPayment = [{
                key: "",
                desc: "Select Payment Method"
            }];
            $.each(paymentData, function(i, v) {
                let sp = v.split(";");
                console.log(sp)
                tempPayment.push({
                    key: sp[0],
                    desc: sp[1]
                });
            })

            this.paymentMethod = tempPayment;
        },
        computed: {
            needVerification() {
                var ret = false;
                var app = this;
                $.each(this.statusList, function(i, v) {
                    if (v.id == app.status_selected) {
                        ret = v.need_verify == "1";
                        return false;
                    }
                });
                return ret;
            },
            totalPrice() {
                var total = 0;
                for (var i in this.transactions) {
                    total += Number(this.transactions[i].price);
                }
                return total;
            },
            transactionsSort() {
                return this.transactions.sort(function(a, b) {
                    return (a.event_pricing_id > b.event_pricing_id) ? -1 : 1;
                })
            },

            total() {
                var total = 0;
                this.eventAdded.forEach((item, index) => {
                    total += parseFloat(item.price);
                })
                return total;
            },
            filteredEvent() {
                var statusSelected = this.status_selected;
                var status = this.statusList.find(data => data.id == statusSelected);
                status = status ? status.kategory : '';

                var events = [];
                if (this.events) {
                    this.events.forEach(function(item, index) {
                        if ($.inArray(status, item.memberStatus) !== -1) {
                            events.push(item);
                        }
                    });
                }
                return events;
            }
        },
        methods: {
            onlyNumber($event) {
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
                formData.set("birthday", birthday);

                // NOTE Data Event dan Payment
                formData.append('eventAdded', JSON.stringify(app.eventAdded));
                formData.append('paymentMethod', app.paymentMethod);

                this.saving = true;
                $.ajax({
                    url: '<?= base_url('member/register'); ?>',
                    type: 'POST',
                    contentType: false,
                    cache: false,
                    processData: false,
                    data: formData
                }).done(function(res) {
                    if (res.statusData == false && res.validation_error) {
                        app.validation_error = res.validation_error
                    } else if (res.statusData == false && res.message) {
                        Swal.fire('Fail', res.message, 'error');
                    } else {
                        app.page = 'payment';
                        app.data = res.data;
                        app.transactions = res.transactions.cart;
                        // app.paymentBank = res.paymentBank.manual;
                    }
                }).fail(function(res) {
                    Swal.fire('Fail', 'Server fail to response !', 'error');
                }).always(function(res) {
                    app.saving = false;
                });
            },
            checkout() {
                var formData = new FormData(this.$refs.form);
                // var birthday = moment(formData.get('birthday')).format("Y-MM-DD");
                var birthday = moment().format("Y-MM-DD");
                formData.set("birthday", birthday);

                // NOTE Data Event dan Payment
                formData.append('data', JSON.stringify(app.data));
                formData.append('selectedPaymentMethod', app.selectedPaymentMethod);

                this.saving = true;
                $.ajax({
                    url: '<?= base_url('member/register/checkout'); ?>',
                    type: 'POST',
                    contentType: false,
                    cache: false,
                    processData: false,
                    data: formData
                }).done(function(res) {
                    if (res.statusData == false && res.validation_error) {
                        app.validation_error = res.validation_error
                    } else if (res.statusData == false && res.message) {
                        Swal.fire('Fail', res.message, 'error');
                    } else {
                        app.page = 'registered';
                    }
                }).fail(function(res) {
                    Swal.fire('Fail', 'Server fail to response !', 'error');
                }).always(function(res) {
                    app.saving = false;
                });
            },
            formatCurrency(price) {
                return new Intl.NumberFormat("id-ID", {
                    style: 'currency',
                    currency: "IDR"
                }).format(price);
            },
            // NOTE Menambah dan Menghapus Event
            addEvent(e, event, member, event_name) {

                if (e.target.checked) {
                    event.member_status = member;
                    event.event_name = event_name;

                    this.eventAdded.push(event);
                } else {
                    this.eventAdded = app.eventAdded.filter(data => data.id != event.id);
                }

            },
            formatDate(date) {
                return moment(date).format("DD MMM YYYY, [At] HH:mm:ss");
            },
        }
    });
    $(function() {
        $(".chosen").chosen().change(function() {
            app.univ_selected = $(this).val();
        });

        // NOTE Status change event set null
        $('#status').change(function(e) {
            e.preventDefault();
            app.status_text = $("#status option:selected").text();
            app.eventAdded = [];
        });

        $(document).on('change', '.selectedPaymentMethod', function(e) {
            e.preventDefault();
            let selected = app.paymentMethod.find(data => data.key == app.selectedPaymentMethod);
            console.log('mantap ', selected, app.selectedPaymentMethod, $(this).val());
            if (selected && selected.key == "espay") {
                $("#modal-select-payment").modal("show");

                var invoiceID = app.data.id_invoice;
                var apiKeyEspay = "<?= Settings_m::getEspay()['apiKey']; ?>";
                var data = {
                    key: apiKeyEspay,
                    paymentId: invoiceID,
                    backUrl: `<?= base_url('member/area'); ?>/redirect_client/billing/${invoiceID}`,
                };
                console.log(data);
                if (typeof SGOSignature !== "undefined") {
                    var sgoPlusIframe = document.getElementById("sgoplus-iframe");
                    if (sgoPlusIframe !== null)
                        sgoPlusIframe.src = SGOSignature.getIframeURL(data);
                    SGOSignature.receiveForm();
                }
            }
        });
    });
</script>
<?php $this->layout->end_script(); ?>