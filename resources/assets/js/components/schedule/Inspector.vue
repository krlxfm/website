<template>
    <div class="card schedule-short-height" style="line-height: 1.3rem">
        <h5 class="card-header" v-if="show && show.name">{{ show.name }}</h5>
        <div class="card-header py-2 pl-3 pr-2" v-else-if="show">
            <div class="d-flex align-items-center">
                <h5 class="mb-0 mr-2">{{ show.title }}</h5>
                <button type="button" class="ml-auto btn btn-sm btn-outline-danger" v-on:click="$emit('remove-show')" v-if="show.day && show.start && show.end">
                    <i class="fas fa-calendar-times"></i>
                </button>
            </div>
        </div>
        <h5 class="card-header" v-else>Show Information</h5>

        <div class="card-body pt-2" v-if="show && show.name">
            <p class="mb-1"><small class="text-muted">Reserved slot, scheduled separately</small></p>
            <div class="d-flex mb-2 align-items-center">
                <span>Grants XP</span>
                <span class="badge badge-success ml-auto" v-if="show.awards_xp">
                    YES
                </span>
                <span class="badge badge-danger ml-auto" v-else>
                    NO
                </span>
            </div>
            <div class="mb-2">
                <small><strong>DESCRIPTION</strong></small><br>
                {{ show.description }}
            </div>
            <div class="mb-2">
                <small><strong>DEFINITION</strong></small><br>
                {{ show.start_day }}, {{ show.start_time }} - {{ show.end_time }}
            </div>
            <a href="#" class="btn btn-primary btn-block mt-3"><i class="fas fa-external-link-alt"></i> Schedule these shows</a>
        </div>
        <div class="card-body pt-3" v-else-if="show">
            <p class="mb-0 text-muted">{{ show.track.name }} | {{ show.id }}</p>
            <p class="mb-0" v-if="!show.day && !show.start && !show.end && !show.published_day && !show.published_start && !show.published_end"></p>
            <p class="mb-0" v-if="(show.day && show.start && show.end) && !(show.published_day && show.published_start && show.published_end)">
                <strong class="text-success">New:</strong> {{ showTime(show.day, show.start, show.end) }}
            </p>
            <p class="mb-0" v-else-if="!(show.day && show.start && show.end) && (show.published_day && show.published_start && show.published_end)">
                <strong class="text-danger">Was:</strong> {{ showTime(show.published_day, show.published_start, show.published_end) }}
            </p>
            <p class="mb-0" v-else-if="show.day == show.published_day && show.start == show.published_start && show.end == show.published_end">
                {{ showTime(show.published_day, show.published_start, show.published_end) }}
            </p>
            <p class="mb-0" v-else>
                <strong class="text-primary">Was:</strong> {{ showTime(show.published_day, show.published_start, show.published_end) }}<br>
                <strong class="text-primary">Now:</strong> {{ showTime(show.day, show.start, show.end) }}
            </p>
            <hr>
            <div class="d-flex align-items-start my-2">
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
                <small><strong>OTHER CONFLICTS</strong></small><br>
                <span v-if="show.conflicts.length == 0">
                    No conflicts declared
                </span>
                <ul class="pl-4 mb-0" v-else>
                    <li v-for="conflict in show.conflicts">
                        {{ conflict.days.join(', ')}}, {{ conflict.start}} - {{ conflict.end}}
                    </li>
                </ul>
            </div>
            <div class="mb-2">
                <small><strong>PREFERENCES</strong></small>
                <span v-if="show.preferences.length == 0">No preferences provided</span>
                <ul class="pl-4 mb-0" v-else>
                    <li v-for="preference in show.preferences.filter(preference => preference.strength == 3)">
                        {{ preference.days.join(', ')}}, {{ preference.start}} - {{ preference.end}} (first choice)
                    </li>
                    <li v-for="preference in show.preferences.filter(preference => preference.strength == 2)">
                        {{ preference.days.join(', ')}}, {{ preference.start}} - {{ preference.end}} (strongly preferred)
                    </li>
                    <li v-for="preference in show.preferences.filter(preference => preference.strength == 1)">
                        {{ preference.days.join(', ')}}, {{ preference.start}} - {{ preference.end}} (preferred)
                    </li>
                </ul>
            </div>
            <a v-bind:href="'/shows/' + show.id" target="_blank" class="btn btn-primary btn-block mt-3"><i class="fas fa-external-link-alt"></i> Full application</a>
        </div>
        <div class="card-body d-flex align-items-center justify-content-center" v-else>
            <p class="mb-0 text-center">
                <strong>No show selected</strong><br>
                (Click or start dragging a show to view its details here.)
            </p>
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
    methods: {
        showTime(day, start, end) {
            const today = moment().format('YYYY-MM-DD');
            return day + ', ' + moment(today + ' ' + start).format('h:mm a') + ' - ' + moment(today + ' ' + end).format('h:mm a');
        }
    },
    props: {
        lengthColors: Array,
        show: Object
    }
}
</script>
