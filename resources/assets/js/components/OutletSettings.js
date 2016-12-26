// OutletSettings.js
import React from 'react';
import ReactDOM from 'react-dom';

class OutletSettings extends React.Component
{
	constructor(props)
	{
		super(props);

		this.state = {
			has_reservation: app.has_reservation,
			has_messaging: app.has_messaging,
			has_menus: app.has_menus
		}

		this.toggleReservation = this.toggleReservation.bind(this)
		this.toggleMessaging = this.toggleMessaging.bind(this)
		this.toggleMenus = this.toggleMenus.bind(this)
	}

	updateSettings()
	{
		let that = this;
		let url = '/dashboard/outlets/' + app.outlet_id + '/settings';

		let data = {
			'has_reservation': this.state.has_reservation,
			'has_messaging': this.state.has_messaging,
			'has_menus': this.state.has_menus
		}

		$.ajax({
			type: 'PUT',
			url: url,
			headers: {
				'X-CSRF-Token': App.csrfToken,
			},
			data: data,
			success: function(response) {
				that.setState({
					has_reservation: response.has_reservation,
					has_messaging: response.has_messaging,
					has_menus: response.has_menus
				});
			}
		})	
	}

	toggleReservation() {
		this.setState({
			has_reservation: ! this.state.has_reservation
		})

		this.updateSettings();
	}

	toggleMessaging() {
		this.setState({
			has_messaging: ! this.state.has_messaging
		})

		this.updateSettings();
	}

	toggleMenus() {
		this.setState({
			has_menus: ! this.state.has_menus
		})

		this.updateSettings();
	}


	render()
	{
		return (
			<div>
				<div className="checkbox-inline">
					<label>
						<input type="checkbox" defaultChecked={this.state.has_reservation} onChange={this.toggleReservation} /> Activate Reservation
					</label>
				</div>
				<div className="checkbox-inline">
					<label>
						<input type="checkbox" defaultChecked={this.state.has_messaging} onChange={this.toggleMessaging} /> Activate Messaging
					</label>
				</div>
				<div className="checkbox-inline">
					<label>
						<input type="checkbox" defaultChecked={this.state.has_menus} onChange={this.toggleMenus} /> Activate Menus
					</label>
				</div>
			</div>
		)
	}
}

ReactDOM.render(
	<OutletSettings />,
	document.getElementById('OutletSettings')
);