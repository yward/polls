<!--
  - @copyright Copyright (c) 2018 René Gieling <github@dartcafe.de>
  -
  - @author René Gieling <github@dartcafe.de>
  -
  - @license GNU AGPL version 3 or any later version
  -
  - This program is free software: you can redistribute it and/or modify
  - it under the terms of the GNU Affero General Public License as
  - published by the Free Software Foundation, either version 3 of the
  - License, or (at your option) any later version.
  -
  - This program is distributed in the hope that it will be useful,
  - but WITHOUT ANY WARRANTY; without even the implied warranty of
  - MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
  - GNU Affero General Public License for more details.
  -
  - You should have received a copy of the GNU Affero General Public License
  - along with this program.  If not, see <http://www.gnu.org/licenses/>.
  -
  -->

<template>
	<Popover class="calendar-peek">
		<template v-if="events.length" #trigger>
			<div class="calendar-peek__conflict icon icon-calendar" />
			<p class="calendar-peek__caption">
				{{ t('polls', 'Conflict') }}
			</p>
		</template>
		<div class="calendar-peek__grid">
			<CalendarInfo v-for="eventItem in sortedEvents"
				:key="eventItem.UID"
				:event="eventItem"
				:option="option" />
		</div>
	</Popover>
</template>

<script>

import { mapState } from 'vuex'
import orderBy from 'lodash/orderBy'
import { Popover } from '@nextcloud/vue'
import CalendarInfo from '../Calendar/CalendarInfo.vue'
import { showError } from '@nextcloud/dialogs'

export default {
	name: 'CalendarPeek',

	components: {
		CalendarInfo,
		Popover,
	},

	props: {
		option: {
			type: Object,
			default: undefined,
		},
	},

	data() {
		return {
			events: [],
		}
	},

	computed: {
		...mapState({
			poll: (state) => state.poll,
		}),

		sortedEvents() {
			const sortedEvents = [...this.events]
			sortedEvents.push(this.thisOption)
			return orderBy(sortedEvents, ['start', 'end'], ['asc', 'asc'])
		},

		thisOption() {
			return {
				id: this.option.id,
				UID: this.option.id,
				calendarKey: 0,
				calendarName: 'Polls',
				displayColor: 'transparent',
				allDay: '',
				description: this.poll.description,
				start: this.option.timestamp,
				location: '',
				end: this.option.timestamp + 3600,
				status: 'self',
				summary: this.poll.title,
			}
		},
	},

	mounted() {
		this.getEvents()
	},

	methods: {
		async getEvents() {
			try {
				const response = await this.$store.dispatch('options/getEvents', { option: this.option })
				this.events = response.data.events
			} catch (e) {
				if (e.message === 'Network Error') {
					showError(t('polls', 'Got a network error while checking calendar events.'))
				}
			}
		},

	},
}

</script>

<style lang="scss">

.calendar-peek__conflict.icon {
	width: 32px;
	height: 32px;
	background-color: var(--color-warning);
	border-radius: 50%;
	margin: 4px auto;
}

.calendar-peek__caption {
	font-size: 0.7em;
}

.calendar-peek__grid {
	padding: 8px;
	background-color: var(--color-main-background);
	border-radius: var(--border-radius);
}

</style>
