<template>
    <table class="table table-hover">
        <thead>
            <tr>
                <th>Preferred times</th>
                <th>Strength</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <tr v-for="(preference, index) in preferences">
                <td class="align-middle" v-html="preferenceToPlainText(preference)"></td>
                <td class="align-middle">
                    {{ preference.strength >= strengthNotes.length ? 'None' : strengthNotes[preference.strength] }}
                </td>
                <td class="align-middle">
                    <div class="btn-group">
                        <button class="btn btn-secondary" type="button" v-on:click="updatePreference(index)"><i class="fas fa-pen"></i> Edit</button>
                        <button class="btn btn-danger" type="button" v-on:click="rmPreference(index)"><i class="fas fa-trash"></i> Remove</button>
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
            preferences: window.preferences,
            strengthNotes: ['None', 'Preferred', 'Strongly Preferred', 'First Choice']
        }
    },
    methods: {
        preferenceToPlainText: function(preference) {
            var text = preference.days.join(", ") + ' ' + to12Hour(preference.start) + ' - ' + to12Hour(preference.end);
            var start = preference.start.split(':').map((item) => { return parseInt(item) });
            var end = preference.end.split(':').map((item) => { return parseInt(item) });
            if(end[0] < start[0] || (end[0] == start[0] && end[1] <= start[1])) {
                text += ' <small class="text-info"><em>NEXT DAY</em></small>';
            }
            return text;
        },
        rmPreference: function(index) {
            removePreference(index);
        },
        updatePreference: function(index) {
            editPreference(index);
        }
    }
}
</script>
