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

	toggleReservation()
	{
		let that = this;
		let type = 'PUT';
		let has_reservation = this.state.has_reservation;
		let url = '/dashboard/outlets/' + app.outlet_id + '/activate-reservation';

		if( has_reservation )
		{
			url = '/dashboard/outlets/' + app.outlet_id + '/deactivate-reservation';
			type = 'DELETE';	
		}

		$.ajax({
			type: type,
			url: url,
			headers: {
				'X-CSRF-Token': App.csrfToken,
			},
			success: function(response) {
				that.setState({
					has_reservation: response.has_reservation
				});
			}
		})		
	}

	toggleMessaging()
	{
		let that = this;
		let type = 'PUT';
		let has_messaging = this.state.has_messaging;
		let url = '/dashboard/outlets/' + app.outlet_id + '/activate-messaging';

		if( has_messaging )
		{
			url = '/dashboard/outlets/' + app.outlet_id + '/deactivate-messaging';
			type = 'DELETE';	
		}

		$.ajax({
			type: type,
			url: url,
			headers: {
				'X-CSRF-Token': App.csrfToken,
			},
			success: function(response) {
				that.setState({
					has_messaging: response.has_messaging
				});
			}
		})		
	}

	toggleMenus()
	{
		let that = this;
		let type = 'PUT';
		let has_menus = this.state.has_menus;
		let url = '/dashboard/outlets/' + app.outlet_id + '/activate-menus';

		if( has_menus )
		{
			url = '/dashboard/outlets/' + app.outlet_id + '/deactivate-menus';
			type = 'DELETE';	
		}

		$.ajax({
			type: type,
			url: url,
			headers: {
				'X-CSRF-Token': App.csrfToken,
			},
			success: function(response) {
				that.setState({
					has_menus: response.has_menus
				});
			}
		})		
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