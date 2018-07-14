<template>
<table class="table table-responsive-sm">
    <thead>
        <tr>
            <th>Host</th>
            <th>Status</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        <tr v-for="(dj, index) in allDJs" v-bind:id="'dj-row' + index">
            <td class="align-middle">
                {{ dj.name }}
                <br>
                <small class="text-muted">{{ dj.email }}</small>
            </td>
            <td class="align-middle">
                {{ dj.membership.accepted ? (dj.membership.boost ? 'Joined with Priority Boost' : 'Joined') : 'Invited' }}
            </td>
            <td class="align-middle" style="width: 200px">
                <button class="btn btn-danger btn-block" v-if="dj.id == userID" v-on:click="leave(index)"><i class="fas fa-sign-out-alt"></i> Leave</button>
                <button class="btn btn-danger btn-block" v-else-if="dj.membership.accepted" v-on:click="removeParticipant(index)"><i class="fas fa-user-minus"></i> Remove</button>
                <button class="btn btn-warning btn-block" v-else v-on:click="removeParticipant(index)"><i class="fas fa-user-minus"></i> Cancel invitation</button>
            </td>
        </tr>
    </tbody>
</table>
</template>

<script>
module.exports = {
    data: function() {
        return {
            allDJs: window.participants,
            userID: window.userID
        }
    },
    methods: {
        leave: function(dj) {
            warnAndDelete(dj, this.allDJs[dj].name, this.allDJs[dj].email, true);
        },
        removeParticipant: function(dj) {
            warnAndDelete(dj, this.allDJs[dj].name, this.allDJs[dj].email, false);
        }
    }
}
</script>
