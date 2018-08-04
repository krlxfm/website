<template>
    <svg width="150" height="750" class="mx-auto" id="schedule-preview">
        <g transform="translate(60,20)" id="schedule-preview-main">
            <g v-for="i in 48" v-bind:transform="'translate(0, ' + ((i - 1) * 13) + ')'">
                <rect v-for="j in 7" width="11" height="11" v-bind:x="(j - 1) * 13" y="0" fill="#dddddd" v-bind:id="'preview-rect-' + time(i, j).format('dddd-HH-mm') "></rect>
                <text x="-5" y="10" text-anchor="end" font-size="12">
                    {{ time(i).format('h:mm a') }}
                </text>
            </g>
        </g>
		<text x="79" y="15" font-size="12" text-anchor="middle">M</text>
		<text x="105" y="15" font-size="12" text-anchor="middle">W</text>
		<text x="130" y="15" font-size="12" text-anchor="middle">F</text>

        <rect x="0" y="660" width="11" height="11" class="svg-bg-priority-a"></rect>
        <text x="15" y="670" font-size="12">Class</text>
        <rect x="0" y="673" width="11" height="11" class="svg-bg-priority-j"></rect>
        <text x="15" y="683" font-size="12">Other conflict</text>
        <rect x="0" y="686" width="11" height="11" class="svg-bg-priority-h"></rect>
		<text x="15" y="696" font-size="12">"Preferred"</text>
        <rect x="0" y="699" width="11" height="11" class="svg-bg-priority-f"></rect>
		<text x="15" y="709" font-size="12">"Strongly preferred"</text>
        <rect x="0" y="712" width="11" height="11" class="svg-bg-priority-d"></rect>
		<text x="15" y="722" font-size="12">"First choice"</text>
    </svg>
</template>

<script>
module.exports = {
    methods: {
        time: function(offset, weekday = 1) {
            return moment().startOf('week').add(30 * (offset - 1), 'm').add(weekday - 1, 'd');
        }
    }
}
</script>
