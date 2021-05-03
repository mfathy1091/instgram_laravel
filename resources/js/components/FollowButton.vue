<template>
    <div>
        <button class="btn btn-primary ml-4" @click="followUser" v-text="buttonText"></button>
    </div>
</template>

<script>
    export default {
        // the passed user id
        props: ['userId', 'follows'],

        mounted() {
            console.log('Component mounted.')
        },

        // set the statuses of the button, whether the authenticated user attached / unattached to the visted profile
        data: function() {
            return {
                status: this.follows,
            }
        },

        methods: {
            followUser() {
                // create the route
                axios.post('/follow/' + this.userId)
                    // this is when we receive a success reponse
                    .then(response => {
                        // toggle status when we receive a success response
                        this.status = ! this.status

                        console.log(response.data);
                    })
                    // this is when we receive errors
                    .catch(errors => {
                        // 401 error raise when non logged in guest press the follow button when we restrict non auth user for using this button
                        if(errors.response.status = 401) {
                            // redirect to the login page
                            window.location = '/login';
                        }
                    });
            }
        },

        // change the button text
        computed: {
            buttonText(){
                return (this.status) ? 'Unfollow' : 'follow'
            }
        }
    }
</script>