<template>
    <table class="table table-hover">
        <thead>
            <tr>
                <th>Conflict times</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <tr v-for="(conflict, index) in conflicts">
                <td class="align-middle" v-html="conflictToPlainText(conflict)"></td>
                <td class="align-middle">
                    <div class="btn-group">
                        <button class="btn btn-secondary" type="button" v-on:click="updateConflict(index)"><i class="fas fa-pen"></i> Edit</button>
                        <button class="btn btn-danger" type="button" v-on:click="rmConflict(index)"><i class="fas fa-trash"></i> Remove</button>
                    </div>
                </td>
            </tr>
        </tbody>
    </table>
</template>

<script>
module.exports = {
    data: function() {
        return {
            conflicts: window.conflicts
        }
    },
    methods: {
        conflictToPlainText: function(conflict) {
            var text = conflict.days.join(", ") + ' ' + to12Hour(conflict.start) + ' - ' + to12Hour(conflict.end);
            var start = conflict.start.split(':').map((item) => { return parseInt(item) });
            var end = conflict.end.split(':').map((item) => { return parseInt(item) });
            if(end[0] < start[0] || (end[0] == start[0] && end[1] <= start[1])) {
                text += ' <small class="text-info"><em>NEXT DAY</em></small>';
            }
            return text;
        },
        rmConflict: function(index) {
            removeConflict(index);
        },
        updateConflict: function(index) {
            editConflict(index);
        }
    }
}
</script>
