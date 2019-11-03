//Component
Vue.component('table-list', {
    template: '<table class="result_table" v-show="rows.length > 0">' +
        '<thead v-show="displaylabel" >' +
        '<tr>' +
        '<th v-for="key in columns" > <!-- Loop through column --> ' +
        '{{ key }} ' +
        '</th> ' +
        '</tr > ' +
        '</thead> ' +
        '<tbody> <tr v-for="row in rows"> <!-- Loop through row --> ' +
        '<td v-for="key in columns">     <!-- Loop through column --> ' +
        '{{ row[key] | capitalize}}' +
        '</td>' +
        '</tr> </tbody> ' +
        '</table>',
    props: {
        displaylabel: { type: Boolean, default: true },
        rows: Array,
        columns: Array
    }, 
    filters: {
        capitalize: function (str) {
            return str.charAt(0).toUpperCase() + str.slice(1)
        }, 
    },
})

//Instance 
var app = new Vue({ //Root Vue Instance
    el: '#app',   //Container ID
    data: {         //Inner variables 
        searchQuery: '', 
        message: '',
        message2: '', 
        name: '',
        email: '',
        columns: ['state', 'name', 'email'],
        rows: []
    },
    methods: {
        getSubscribers: function(){ 
            this.message2 = ''; 
            axios.get('/subscriber/' + this.searchQuery, { headers: { "Authorization": `Bearer ` + TOKEN } } )
                .then(response => {  
                    if(response.data != ""){
                        this.rows = response.data; 
                    }else{
                        this.rows = [];
                        this.message2 = 'No record found!';
                    }  
                })
                .catch(error => {
                    console.log(error);
                });
        },
        addSubscriber: function(){
            this.message = "Processing..."; 
            axios.post('/subscriber', { name: this.name, email: this.email, headers: { "Authorization": `Bearer ` + TOKEN } })
                .then(response => {   
                    this.message = response.data;
                })
                .catch(error => {
                    //console.log(error);
                    this.message = error;
                });
        }, 
    },
    filters: {

    }
});