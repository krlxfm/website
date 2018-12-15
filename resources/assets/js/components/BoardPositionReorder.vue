<template>
    <div>
        <div class="list-group">
            <div class="list-group-item d-flex align-items-center" v-bind:data-position-id="position.id" v-for="position, index in positions">
                <div>
                    <span class="badge" v-bind:class="position.dark ? 'badge-dark' : 'badge-light'" v-bind:style="'background: ' + position.color">
                        {{ position.abbr }}
                    </span>
                    {{ position.title }}
                </div>
                <div class="btn-group ml-auto">
                    <button class="btn btn-primary" v-if="index > 0" v-on:click="moveUp(index)"><i class="fas fa-arrow-up"></i> Move up</button>
                    <button class="btn btn-secondary" v-if="index < (positions.length - 1)" v-on:click="moveDown(index)"><i class="fas fa-arrow-down"></i> Move down</button>
                </div>
            </div>
        </div>
        <input id="position-order" type="hidden" name="order" v-bind:value="positions.map(position => position.id).join(',')">
    </div>
</template>

<script>
module.exports = {
    data: function() {
        return {
            positions: window.positions
        }
    },
    methods: {
        moveUp: function(index) {
            positions[index - 1] = positions.splice(index, 1, positions[index - 1])[0];
        },
        moveDown: function(index) {
            positions[index] = positions.splice(index + 1, 1, positions[index])[0];
        }
    }
}
</script>
