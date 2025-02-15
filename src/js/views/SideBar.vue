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
	<AppSidebar ref="sideBar"
		:active="active"
		:title="t('polls', 'Details')"
		@close="closeSideBar()">
		<AppSidebarTab v-if="acl.allowEdit"
			:id="'configuration'"
			:order="1"
			:name="t('polls', 'Configuration')"
			icon="icon-mask-md-sidebar-configuration">
			<SideBarTabConfiguration />
		</AppSidebarTab>

		<AppSidebarTab v-if="acl.allowEdit"
			:id="'options'"
			:order="2"
			:name="t('polls', 'Options')"
			icon="icon-mask-md-sidebar-options">
			<SideBarTabOptions />
		</AppSidebarTab>

		<AppSidebarTab v-if="acl.allowEdit"
			:id="'sharing'"
			:order="3"
			:name="t('polls', 'Sharing')"
			icon="icon-mask-md-sidebar-share">
			<SideBarTabShare />
		</AppSidebarTab>

		<AppSidebarTab v-if="acl.loggedIn && useCollaboration"
			:id="'collaboration'"
			:order="4"
			:name="t('polls', 'Collaboration')"
			icon="icon-projects">
			<SideBarTabCollaboration />
		</AppSidebarTab>

		<AppSidebarTab v-if="acl.allowComment"
			:id="'comments'"
			:order="5"
			:name="t('polls', 'Comments')"
			icon="icon-mask-md-sidebar-comments">
			<SideBarTabComments />
		</AppSidebarTab>

		<AppSidebarTab v-if="acl.allowEdit && useActivity"
			:id="'activity'"
			:order="6"
			:name="t('polls', 'Activity')"
			icon="icon-activity">
			<SideBarTabActivity />
		</AppSidebarTab>
	</AppSidebar>
</template>

<script>
import { AppSidebar, AppSidebarTab } from '@nextcloud/vue'
import { mapState } from 'vuex'
import { emit } from '@nextcloud/event-bus'

export default {
	name: 'SideBar',

	components: {
		SideBarTabConfiguration: () => import('../components/SideBar/SideBarTabConfiguration.vue'),
		SideBarTabComments: () => import('../components/SideBar/SideBarTabComments.vue'),
		SideBarTabOptions: () => import('../components/SideBar/SideBarTabOptions.vue'),
		SideBarTabShare: () => import('../components/SideBar/SideBarTabShare.vue'),
		SideBarTabCollaboration: () => import('../components/SideBar/SideBarTabCollaboration.vue'),
		SideBarTabActivity: () => import('../components/SideBar/SideBarTabActivity.vue'),
		AppSidebar,
		AppSidebarTab,
	},

	props: {
		active: {
			type: String,
			default: t('polls', 'Comments').toLowerCase(),
		},
	},

	computed: {
		...mapState({
			poll: (state) => state.poll,
			acl: (state) => state.poll.acl,
			useActivity: (state) => state.appSettings.useActivity,
			useCollaboration: (state) => state.appSettings.useCollaboration,
		}),
	},
	methods: {
		closeSideBar() {
			emit('polls:sidebar:toggle', { open: false })
		},
	},

}

</script>
