<template>
    <div>
        <div class="form-group">
            <input type="text" class="form-control" v-on:input="search" placeholder="Name, username, or email" name="search" id="add-host-search">
        </div>
        <div v-if="suggestions.length > 0" class="new-participant-suggestions">
            <div class="list-group">
                <div class="list-group-item d-flex align-items-center" v-for="(dj, index) in suggestions">
                    <div>
                        {{ dj.full_name }}
                        <br>
                        <small class="text-muted">{{ dj.email }}</small>
                    </div>
                    <button class="ml-auto btn btn-light" v-if="djInShow(dj.id)" disabled>
                        <i class="fas fa-check"></i> Invited
                    </button>
                    <button type="button" class="ml-auto btn btn-success" v-else v-on:click="invite(index)" v-bind:data-email="dj.email">
                        <i class="fas fa-user-plus"></i> Invite
                    </button>
                </div>
            </div>
        </div>
        <div v-else class="d-flex w-100 justify-content-center align-items-center new-participant-suggestions">
            <div v-if="noResults" style="margin: auto" class="text-center">
                <p>No results found for "{{ value }}".</p>
                <div class="alert alert-warning" v-if="value.indexOf(' ') >= 0">
                    We've attempted to guess {{ value }}'s Carleton email as <strong>{{ email}}</strong>. If this is incorrect (most common if the email ends in a number), please enter the correct address above.
                </div>
                <button type="button" class="btn btn-success" v-on:click="inviteByEmail(email)">
                    Invite {{ email }} by email
                </button>
            </div>
            <span v-else style="margin: auto" class="text-center">Start typing in the field above to add a co-host</span>
        </div>
    </div>
</template>

<script>
module.exports = {
    data: function () {
        return {
            value: '',
            timer: null,
            noResults: false,
            suggestions: [],
            currentDJs: window.participants
        }
    },
    methods: {
        djInShow: function(id) {
            var found = false;
            this.currentDJs.forEach((dj) => {
                if(dj.id == id) found = true;
            });
            return found;
        },
        search: function() {
            this.value = event.target.value;
            clearTimeout(this.timer);
            this.timer = setTimeout(this.sendSearch, 500);
        },
        sendSearch: function() {
            axios.get('/api/v1/users', { params: { query: this.value } })
            .then((response) => {
                this.suggestions = response.data;
                this.noResults = (response.data.length == 0);
            })
            .catch((error) => {
                this.suggestions = [];
                this.noResults = false;
            })
        },
        cleanup: function() {
            $("#participant-add").modal('hide');
            $("#add-host-search").val('');
            this.value = '';
            this.noResults = false;
            this.suggestions = [];
        },
        inviteByEmail: function(email) {
            axios.patch('/api/v1/shows/'+window.showID+'/hosts', {
                "add": [email]
            })
            .then(() => {
                return swal({
                    title: "Invitation Sent!",
                    text: "An invitation has been sent to "+email+".",
                    icon: "success"
                })
            })
            .then(() => {
                this.cleanup();
            })
        },
        invite: function(index, event) {
            axios.patch('/api/v1/shows/'+window.showID+'/hosts', {
                "add": [this.suggestions[index].email]
            })
            .then(() => {
                window.participants.push({
                    id: this.suggestions[index].id,
                    name: this.suggestions[index].name,
                    full_name: this.suggestions[index].full_name,
                    email: this.suggestions[index].email,
                    membership: {
                        accepted: false
                    }
                });
                this.cleanup();
            })
        }
    },
    computed: {
        email: function() {
            if(this.value.indexOf(' ') > -1) {
                var components = this.value.split(' ');
                return components[components.length - 1].toLowerCase() + components[0][0].toLowerCase() + '@carleton.edu';
            } else if(this.value.indexOf('@') > -1) {
                return this.value.substr(0, this.value.indexOf('@')) + '@carleton.edu';
            } else {
                return this.value.toLowerCase() + "@carleton.edu";
            }
        }
    }
}
</script>
