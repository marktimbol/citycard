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
			has_menus: app.has_menus,
			isUpdating: false,
			updateButtonText: 'Update'
		}

		this.toggleReservation = this.toggleReservation.bind(this)
		this.toggleMessaging = this.toggleMessaging.bind(this)
		this.toggleMenus = this.toggleMenus.bind(this)
		this.updateSettings = this.updateSettings.bind(this)
	}

	updateSettings()
	{
		this.setState({
			isUpdating: true,
			updateButtonText: 'Updating'
		})

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
					has_menus: response.has_menus,
					isUpdating: false,
					updateButtonText: 'Update'						
				});
			}
		});
	}

	toggleReservation() {
		this.setState({
			has_reservation: this.state.has_reservation == 0 ? 1 : 0
		})
	}

	toggleMessaging() {
		this.setState({
			has_messaging: this.state.has_messaging == 0 ? 1 : 0
		})
	}

	toggleMenus() {
		this.setState({
			has_menus: this.state.has_menus == 0 ? 1 : 0
		})
	}


	render()
	{
		return (
			<div>
				<h3>Outlet Settings</h3>
				<ul className="list-group">
					<li className="list-group-item">
						<div className="checkbox-inline">
							<label>
								<input type="checkbox" defaultChecked={this.state.has_reservation} onChange={this.toggleReservation} /> Activate Reservation
							</label>
						</div>
					</li>
					<li className="list-group-item">
						<div className="checkbox-inline">
							<label>
								<input type="checkbox" defaultChecked={this.state.has_messaging} onChange={this.toggleMessaging} /> Activate Messaging
							</label>
						</div>
					</li>
					<li className="list-group-item">
						<div className="checkbox-inline">
							<label>
								<input type="checkbox" defaultChecked={this.state.has_menus} onChange={this.toggleMenus} /> Activate Menus
							</label>
						</div>
					</li>
					<li className="list-group-item">
						<button className="btn btn-sm btn-default" onClick={this.updateSettings} disabled={this.state.isUpdating}>
							{ this.state.isUpdating ? <span></span> : <span><i className="fa fa-check"></i>&nbsp; </span> }
							{ this.state.updateButtonText }
							{ this.state.isUpdating ? 
								<span>&nbsp; <i className="fa fa-spinner fa-spin"></i></span> : 
								<span></span> 
							}
						</button>
					</li>
				</ul>
			</div>
		)
	}
}

ReactDOM.render(
	<OutletSettings />,
	document.getElementById('OutletSettings')
);