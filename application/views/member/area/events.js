export default Vue.component("PageEvents", {
    template: `
        <div class="col-lg-9">
            <page-loader :loading="loading" :fail="fail"></page-loader>
            <div v-if="user.verified_by_admin == 0" class="alert alert-info">
                <h4>Your status is under review</h4>
                <p>The current administrators need to review and approve your status. Please return to check your status later.
                You will be sent an email when a decision has been made, and <strong>you cannot follow an event before your status accepted</strong></p>
            </div>
            <div v-else >
                <div class="row">
                    <div class="col-md-9">
                    <div class="overflow-hidden mb-1">
                        <h2 class="font-weight-normal text-7 mb-0"><strong class="font-weight-extra-bold">Events</strong></h2>
                    </div>
                    <div class="overflow-hidden mb-4 pb-3">
                        <p class="mb-0">Please select the event you want. *Event available based on your status and date</p>
                    </div>
                    </div>
                    <div class="col-md-3">
                        <router-link class="btn btn-primary mt-4" to="/billing"><span style="font-size: 12px" class="badge badge-primary">2</span><i class="fa fa-shopping-cart fa-2x"></i> </router-link>
                    </div>
                </div>
                
                <div class="row">
                    <div class="accordion accordion-quaternary col-md-12">
                        <div  v-for="(event, index) in events" class="card card-default">
                            <div class="card-header">
                                <h4 class="card-title m-0">
                                    <a class="accordion-toggle" data-toggle="collapse" :href="'#accordion-'+index" aria-expanded="true">
                                        {{ event.name }}
                                    </a>
                                </h4>
                            </div>
                            <div :id="'accordion-'+index" :class="[index==0?'show':'']" class="collapse">
                                    <table class="table">
                                        <thead>
                                            <tr>
                                                <th>Category</th>
                                                <th v-for="name in event.pricingName"><span v-html="name.title"></span></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr v-for="member in event.memberStatus">
                                                <td>{{ member }}</td>
                                                <td v-for="name in event.pricingName" class="text-center">
                                                    {{ event.pricing[name.name][member] }}<br/>
                                                    <button class="btn btn-sm btn-warning">Add To Cart</button>
                                                    <button disabled class="btn btn-sm btn-warning">Not Available</button>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    `,
    data: function () {
        return {
            loading: false,
            fail: false,
            user: {},
            events: [
                {
                    name: 'Symposium',
                    pricingName: [{
                        name: 'Early Bird',
                        title: 'Early Bird <br/> 1 Januari 2019 - 20 Januari 2019'
                    }, {name: 'Regular', title: 'Regular <br/> 1 Januari 2019 - 20 Januari 2019'}, {
                        name: 'OnSite',
                        title: 'OnSite <br/> 1 Januari 2019 - 20 Januari 2019'
                    }],
                    memberStatus: ['Perdoski Member', 'Young Member', 'General Practitioner'],
                    pricing: {
                        'Early Bird': {'Perdoski Member': '120', 'Young Member': 130, 'General Practitioner': 140},
                        'Regular': {'Perdoski Member': '120', 'Young Member': 130, 'General Practitioner': 140},
                        'OnSite': {'Perdoski Member': '120', 'Young Member': 130, 'General Practitioner': 140}
                    }
                },
                {name: 'Workshop'}
            ]
        }
    },
});