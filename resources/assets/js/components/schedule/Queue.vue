<template>
    <div class="card schedule-full-height">
        <h5 class="card-header">Queue</h5>
        <ul class="list-group list-group-flush">
            <li class="list-group-item" v-for="show in shows">
                {{ show.title }}
            </li>
        </ul>
    </div>
</template>

<script>
module.exports = {
    props: {
        shows: Array
    }
}
</script>
