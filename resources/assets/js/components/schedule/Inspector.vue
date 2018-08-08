<template>
    <div class="card schedule-short-height" style="line-height: 1.3rem">
        <h5 class="card-header" v-if="show">{{ show.title }}</h5>
        <h5 class="card-header" v-else>Show Information</h5>

        <div class="card-body pt-2" v-if="show">
            <p class="mb-1"><small class="text-muted">{{ show.track.name }} | {{ show.id }}</small></p>
            <div class="d-flex align-items-start mb-2">
                <span class="badge" v-bind:class="'bg-priority-'+show.priority.charAt(0).toLowerCase()" style="margin-top: 2px">
                    {{ show.priority }}
                </span>
                <div class="ml-2" style="line-height: 1.3rem" v-html="show.hosts.map(host => host.full_name).join('<br>') "></div>
            </div>
            <div class="mb-2" v-if="show.notes">
                <small><strong>SCHEDULING NOTES</strong></small><br>
                {{ show.notes }}
            </div>
            <div class="mb-2">
                <small><strong>BASIC PREFERENCES</strong></small><br>
                <div class="d-flex align-items-center" v-for="(status, slot) in show.special_times">
                    <span>{{ specials[slot].name }}</span>
                    <span class="badge ml-auto" v-bind:class="'badge-' + specialBadges[status].color">
                        {{ specialBadges[status].text }}
                    </span>
                </div>
                <div class="d-flex align-items-center">
                    <span>Preferred length</span>
                    <span class="badge ml-auto" v-bind:class="lengthColors[show.preferred_length / 30]">
                        {{ show.preferred_length }}m
                    </span>
                </div>
            </div>
            <div class="mb-2">
                <small><strong>CLASSES</strong></small><br>
                {{ show.classes.join(', ') }}
            </div>
            <div class="mb-2">
                <small><strong>CONFLICTS</strong></small><br>
                <span v-if="show.conflicts.length == 0">
                    No conflicts declared
                </span>
                <span v-else-if="show.conflicts.length == 1">
                    One conflict declared
                </span>
                <span v-else>
                    {{ show.conflicts.length }} conflicts declared
                </span>
            </div>
            <div class="mb-2">
                <small><strong>PREFERENCES</strong></small><br>
                {{ show.preferences.filter(preference => preference.strength == 1).length }} preferred times<br>
                {{ show.preferences.filter(preference => preference.strength == 2).length }} strongly preferred times<br>
                {{ show.preferences.filter(preference => preference.strength == 3).length }} first-choice times
            </div>
            <a href="#" class="btn btn-primary btn-block mt-3"><i class="fas fa-external-link-alt"></i> Full application</a>
        </div>
        <div class="card-body d-flex" v-else>
            No show selected
        </div>
    </div>
</template>

<script>
module.exports = {
    data: function() {
        return {
            specials: window.specials,
            specialBadges: {y: {color: "success", text: "YES"}, m: {color: "warning", text: "MEH"}, n: {color: "danger", text: "NO"}}
        }
    },
    props: {
        lengthColors: Array,
        show: Object
    }
}
</script>
