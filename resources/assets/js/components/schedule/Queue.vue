<template>
    <div class="card schedule-full-height">
        <h5 class="card-header">Queue</h5>
        <ul class="list-group list-group-flush">
            <li class="list-group-item schedule-queue-item" v-for="show in showsWithoutTimes" v-bind:data-show-id="show.id" v-bind:key="show.id">
                <h5 class="head-sans-serif mb-0">{{ show.title }}</h5>
                <p class="mb-0">
                    <span class="badge" v-bind:class="'bg-priority-'+show.priority.charAt(0).toLowerCase()">
                        {{ show.priority }}
                    </span>
                    <span class="badge bg-priority-a">
                        <i class="fas" v-bind:class="show.hosts.length == 1 ? 'fa-user' : 'fa-users'"></i>
                        {{ show.hosts.length }}
                    </span>
                    <span class="badge" v-bind:class="lengthColors[show.preferred_length / 30]">
                        <i class="fas fa-clock"></i>
                        {{ show.preferred_length }}m
                    </span>
                </p>
            </li>
        </ul>
    </div>
</template>

<script>
module.exports = {
    data: function() {
        return {
            lengthColors: ['badge-light', 'badge-primary', 'badge-success', 'badge-warning', 'badge-danger']
        }
    },
    props: {
        shows: Array
    },
    mounted: function () {
        window.enableDragging();
    },
    updated: function () {
        window.enableDragging();
    },
    computed: {
        showsWithoutTimes: function() {
            return this.shows.filter((show) => {
                return show.start == null || show.end == null || show.day == null;
            })
        }
    }
}
</script>
