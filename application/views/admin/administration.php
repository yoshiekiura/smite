<?php
/**
 * @var $event
 */
$this->layout->begin_head();
?>
<style>
	input.disabled {
		border: none;
		background: none;
	}
	a.disabled{
		pointer-events: none;
		cursor: default;
		color: white !important;
	}

</style>
<?php $this->layout->end_head(); ?>
<div class="header bg-gradient-primary pb-8 pt-5 pt-md-8" xmlns:v-bind="http://www.w3.org/1999/xhtml"
	 xmlns:v-bind="http://www.w3.org/1999/xhtml"></div>

<div class="container-fluid mt--7">
	<div key="table" class="row">
		<div class="col-xl-12">
			<div class="card shadow">
				<div class="card-header">
					<div class="row">
						<div class="col-6">
							<h3>Administration</h3>
						</div>
						<div class="col-6 text-right">
							<div class="row">
								<div class="col-7">
									<select class="form-control" v-model="selectedEvent" @change="fetchData(true)">
										<option disabled hidden value="">Select Event First</option>
										<option v-for="(event,key) in eventList" :value="key"> {{ event }}</option>
									</select>
								</div>
								<div class="col-5">
									<div class="btn-group" role="group">
										<button :disabled="!selectedEvent || selectedEvent <= 0" id="btnGroupDrop1" type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
											Download All
										</button>
										<div class="dropdown-menu" aria-labelledby="btnGroupDrop1">
											<a target="_blank" class="dropdown-item" :href="'<?=base_url('admin/administration/download_all/certificate');?>/'+selectedEvent">Certificate</a>
											<a target="_blank" class="dropdown-item" :href="'<?=base_url('admin/administration/download_all/nametag');?>/'+selectedEvent">Name Tag</a>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
				<div class="table-responsive">
					<div class="row mb-2 mt-2">
						<div class="col form-inline ml-3">
							<label>
								Show&nbsp;
								<select v-model="pageSize" class="form-control form-control-sm">
									<option v-for="c in perPage" :value="c">{{ c }}</option>
								</select>&nbsp;Entries
							</label>
							&nbsp;
							<label v-if="isScan" class="badge badge-info">Result Of Scanning QR-Code</label>
						</div>
						<div class="col mr-3">
							<div class="input-group">
								<input type="text" v-model="globalFilter" @keyup.enter="doFilter"
									   placeholder="Type to search !" class="form-control"/>
								<div class="input-group-append">
									<button type="button" v-on:click="doFilter" class="btn btn-primary"><i
											class="fa fa-search"></i> Search
									</button>
									<button type="button" v-on:click="resetFilter" class="btn btn-primary"><i
											class="fa fa-times"></i> Reset
									</button>

									<button type="button" v-on:click="openScanner" class="btn btn-primary"><i
											class="fa fa-barcode"></i> Scan
									</button>
								</div>
							</div>
						</div>
					</div>
					<div style="position: relative">
						<div v-if="loading" class="bg-color-dark"
							 style="width: 100%;height: 100%;position: absolute; z-index: 1000;background-color: darkgrey;opacity: .5">
							<div class="fa fa-spin fa-cog text-primary fa-4x" role="status"
								 style="position:absolute;opacity:1;top: 45%;left: 45%;">
								<span class="sr-only">Loading...</span>
							</div>
						</div>
						<vuetable ref="vuetable"
								  :api-mode="false"
								  :fields="fields"
								  :data="localData"
								  :data-total="dataCount"
								  :data-manager="dataManager"
								  data-path="data"
								  pagination-path="pagination"
								  :per-page="pageSize"
								  :css="css.table"
								  @vuetable:pagination-data="onPaginationData">
							<template slot="fullname" slot-scope="props">
								<label v-show="!props.rowData.editable">{{ props.rowData.fullname }}</label>
								<input v-show="props.rowData.editable" class="form-control form-control-sm" type="text" v-model="props.rowData.fullname"/>
								<div class="float-right">
								<a href="#" v-show="!props.rowData.editable"
								   v-on:click.prevent="editName(props.rowData)" class="badge badge-info">Change</a>
								<a href="#" v-show="props.rowData.editable"
								   v-on:click.prevent="saveName(props.rowData,$event)"
								   class="badge badge-success">Save</a>
								<a href="#" v-show="props.rowData.editable" :class="{disabled:props.rowData.saving}"
								   v-on:click.prevent="closeName(props.rowData)" class="badge badge-danger">Cancel</a>
								</div>
							</template>
							<template slot="name_tag" slot-scope="props">
								<input type="checkbox" v-model="props.rowData.checklist.nametag" class="ttip"
									   @change="changeChecklist(props.rowData)" :data-original-title="'Event Name : '+props.rowData.event_name"/>
							</template>
							<template slot="seminar_kit" slot-scope="props">
								<input type="checkbox" v-model="props.rowData.checklist.seminarkit" class="ttip"
									   @change="changeChecklist(props.rowData)"  :data-original-title="'Event Name : '+props.rowData.event_name"/>
							</template>
							<template slot="taker" slot-scope="props">
								<input type="text" class="form-control" v-model="props.rowData.checklist.taker"
									   @change="changeChecklist(props.rowData)">
							</template>
							<template slot="event_id" slot-scope="props">
								<div class="btn-group" role="group">
									<button id="btnGroupDrop2" type="button" class="btn btn-primary btn-sm dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
										Download
									</button>
									<div class="dropdown-menu" aria-labelledby="btnGroupDrop2">
										<a target="_blank" class="dropdown-item" :href="'<?=base_url('admin/administration/certificate');?>/'+props.rowData.event_id+'/'+props.rowData.id">Certificate</a>
										<a target="_blank" class="dropdown-item" :href="'<?=base_url('admin/administration/card');?>/'+props.rowData.event_id+'/'+props.rowData.id">Name Tag</a>
									</div>
								</div>
							</template>
						</vuetable>
						<div class="row mt-3 mb-3">
							<vuetable-pagination-info ref="paginationInfo"
													  no-data-template="No Data Available !"
													  :css="css.info"></vuetable-pagination-info>
							<vuetable-pagination ref="pagination"
												 :css="css.pagination"
												 @vuetable-pagination:change-page="onChangePage"
							></vuetable-pagination>
						</div>
					</div>
				</div>
			</div>

		</div>
	</div>
</div>
<div class="modal" id="modal-scanner">
	<div class="modal-dialog modal-sm">
		<div class="modal-content">

			<!-- Modal Header -->
			<div class="modal-header">
				<h4 class="modal-title">Scan Nametag or Registration Proof</h4>
			</div>

			<!-- Modal body -->
			<div class="modal-body">
				<div class="col-sm-12 text-center">
					<video style="border:solid 3px;height: 100%;width: 100%" muted playsinline id="qr-video"
						   ref="qrVideo"></video>
				</div>
			</div>

			<!-- Modal footer -->
			<div class="modal-footer">
				<button type="button" class="btn btn-danger" v-on:click="closeScanner">Close</button>
			</div>

		</div>
	</div>
</div>
<!-- Table -->

<?php $this->layout->begin_script(); ?>
<script src="https://cdn.jsdelivr.net/npm/lodash@4.17.15/lodash.min.js"></script>
<script type="module">
	import QrScanner from "<?=base_url("themes/script/qr-scanner.min.js");?>";

	QrScanner.WORKER_PATH = '<?=base_url("themes/script/qr-scanner-worker.min.js");?>';
	var scanner = null;

	var app = new Vue({
		el: '#app',
		components: {
			'vuetable-pagination': Vuetable.VuetablePagination,
			'vuetable-pagination-info': Vuetable.VuetablePaginationInfo
		},
		data: {
			isScan:false,
			resultScan:[],
			fields: [{name: 'fullname', sortField: 'fullname'}, {
				name: 'name_tag',
				title: "Name Tag"
			}, {name: 'seminar_kit', title: "Seminar Kit"}, {name: 'taker', title: "Taker"},{name:'event_id',title:'Action'}],
			eventList: <?=json_encode($event);?>,
			selectedEvent: "",
			backupName:{},
			localData: {data: [], pagination: {}},
			pageSize: 10,
			globalFilter: '',
			perPage: [10, 20, 50, 100],
			loading: false,
			dataCount: 0,
			css: {
				table: {
					tableWrapper: '',
					tableHeaderClass: 'mb-0',
					tableBodyClass: 'mb-0',
					tableClass: 'table table-bordered table-stripped table-hover table-grid',
					loadingClass: 'loading',
					ascendingIcon: 'fa fa-chevron-up',
					descendingIcon: 'fa fa-chevron-down',
					ascendingClass: 'sorted-asc',
					descendingClass: 'sorted-desc',
					sortableIcon: 'fa fa-sort',
					detailRowClass: 'vuetable-detail-row',
					handleIcon: 'fa fa-bars text-secondary',
					renderIcon(classes, options) {
						return `<i class="${classes.join(' ')}"></span>`
					}
				},
				info: {
					infoClass: 'pl-4 col'
				},
				pagination: {
					wrapperClass: 'col pagination',
					activeClass: 'active',
					disabledClass: 'disabled',
					pageClass: 'btn btn-border',
					linkClass: 'btn btn-border',
					icons: {
						first: '',
						prev: '',
						next: '',
						last: '',
					},
				}
			},
		},
		created() {
			this.fetchData();
		},
		methods: {
			editName(row) {
				this.backupName[row.id] = row.fullname;
				row.editable = true;
			},
			openScanner(){
				const video = app.$refs.qrVideo;
				var temp = "";
				function setResult(rs) {
					if(temp != rs) {
						temp = rs;
						app.resultScan = rs.split(";");
						app.isScan = true;
						console.log(rs);
						app.fetchData(false);
						app.closeScanner();
					}
				}
				scanner = new QrScanner(video, result => setResult(result));
				scanner.start();
				scanner.setInversionMode("both");
				$("#modal-scanner").modal("show");
			},
			closeScanner() {
				if (scanner) {
					scanner.stop();
				}
				$("#modal-scanner").modal("hide");
			},
			saveName(row, event) {
				if(row.saving)
					return false;
				var member = {
					fullname: row.fullname,
					gender: row.gender,
					phone: row.phone,
					city: row.city,
					address: row.address,
					univ: row.univ,
					id: row.id
				};
				row.saving = true;
				event.target.innerHTML = "<i class='fa fa-spin fa-spinner'></i>";
				event.target.setAttribute("disabled", "disabled");
				$.post("<?=base_url('admin/member/save_profile');?>", member, function (res) {
					row.editable = false;
					Swal.fire("Success", "Name has been saved!", "success");
				}).fail(function (xhr) {
					var message =  xhr.getResponseHeader("Message");
					if(!message)
						message = 'Server fail to response !';
					Swal.fire('Fail', message, 'error');
				}).always(function () {
					event.target.innerHTML = "Save";
					event.target.removeAttribute("disabled");
					row.saving = false;
				});
			},
			closeName(row) {
				if(row.saving)
					return false;
				row.editable = false;
				row.fullname = this.backupName[row.id];
			},
			dataManager(sortOrder, pagination) {
				let data = this.localData.data
				// account for search filter
				if (this.globalFilter) {
					// the text should be case insensitive
					let txt = new RegExp(this.globalFilter, 'i')

					// search on name, email, and nickname
					data = _.filter(data, function (item) {
						return item.fullname.search(txt) >= 0
					})
				}

				// sortOrder can be empty, so we have to check for that as well
				if (sortOrder.length > 0) {
					data = _.orderBy(data, sortOrder[0].sortField, sortOrder[0].direction)
				}

				// since the filter might affect the total number of records
				// we can ask Vuetable to recalculate the pagination for us
				// by calling makePagination(). this will make VuetablePagination
				// work just like in API mode
				pagination = this.$refs.vuetable.makePagination(data.length)

				// if you don't want to use pagination component, you can just
				// return the data array
				return {
					pagination: pagination,
					data: _.slice(data, pagination.from - 1, pagination.to)
				}
			},
			onPaginationData(paginationData) {
				this.$refs.pagination.setPaginationData(paginationData);
				this.$refs.paginationInfo.setPaginationData(paginationData);
			},
			onChangePage(page) {
				this.$refs.vuetable.changePage(page);
			},
			doFilter() {
				Vue.nextTick(() => this.$refs.vuetable.refresh())
			},
			resetFilter() {
				this.globalFilter = "";
				if(this.isScan){
					this.isScan = false;
					this.localData = {data: [], pagination: {}};
				}
				Vue.nextTick(() => this.$refs.vuetable.refresh())
			},
			changeChecklist(row) {
				$.post("<?=base_url("admin/member/save_check");?>", {
					transaction: [{
						checklist: row.checklist,
						id: row.td_id
					}]
				});
			},
			fetchData(fromEventChange) {
				if(fromEventChange)
					this.isScan = false;

				if (this.selectedEvent != "" || this.isScan) {
					var app = this;
					this.loading = true;
					var param = {};
					if(app.isScan && app.resultScan.length > 0){
						if(app.resultScan[0] == "card"){
							param.member_id = app.resultScan[1];
							param.event_id = app.resultScan[2];
						}else{
							param.transaction_id = app.resultScan[1];
						}
					}else{
						param.id = this.selectedEvent;
					}
					$.post("<?=base_url('admin/administration/get_participant');?>", param, function (res) {
						if (res.status) {
							$.each(res.data, function (i, v) {
								if (!v.checklist)
									v.checklist = {nametag: false, seminarkit: false, taker: ""};
								else {
									try {
										v.checklist = JSON.parse(v.checklist);
										v.checklist.nametag = (v.checklist.nametag == "true");
										v.checklist.seminarkit = (v.checklist.seminarkit == "true");
									} catch (e) {
										v.checklist = {nametag: false, seminarkit: false, taker: ""};
									}
								}
							});
							app.localData = {
								data: res.data,
								"pagination": {
									"total": res.data.length,
									"per_page": 10,
									"current_page": 1,
									"last_page": Math.ceil(res.data.length / 10),
									"next_page_url": null,
									"prev_page_url": null,
									"from": 1,
									"to": 10,
								}
							};
							Vue.nextTick(function () {
								app.$refs.vuetable.refresh();
								$(".ttip").tooltip({placement:'bottom'});
							})
						}
					}).fail(function (xhr) {
						var message =  xhr.getResponseHeader("Message");
						if(!message)
							message = 'Server fail to response !';
						Swal.fire('Fail', message, 'error');
					}).always(function () {
						app.loading = false;
					});
				}
			}
		}
	});
</script>
<?php $this->layout->end_script(); ?>
