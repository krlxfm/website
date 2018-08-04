<template>
    <div>
        <p v-for="conflict in conflicts">
            {{ conflict.start }}
        </p>
    </div>
</template>

<script>
module.exports = {
    data: function () {
        return {
            conflicts: window.conflicts,
            preferences: window.preferences
        }
    }
}
</script>
