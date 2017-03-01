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
			is_open: app.is_open,
			isUpdating: false,
			updateButtonText: 'Update'
		}

		this.toggleReservation = this.toggleReservation.bind(this)
		this.toggleMessaging = this.toggleMessaging.bind(this)
		this.toggleMenus = this.toggleMenus.bind(this)
		this.toggleOpen = this.toggleOpen.bind(this)
		this.updateSettings = this.updateSettings.bind(this)
	}

	updateSettings()
	{
		this.setState({
			isUpdating: true,
			updateButtonText: 'Updating'
		})

		let that = this;
		let data = {
			'has_reservation': that.state.has_reservation,
			'has_messaging': that.state.has_messaging,
			'has_menus': that.state.has_menus,
			'is_open': that.state.is_open
		}

		axios.put(app.update_settings_route, {
			has_reservation: that.state.has_reservation,
			has_messaging: that.state.has_messaging,
			has_menus: that.state.has_menus,
			is_open: that.state.is_open				
		}).then(function(response) {
			that.setState({
				has_reservation: response.has_reservation,
				has_messaging: response.has_messaging,
				has_menus: response.has_menus,
				is_open: response.is_open,
				isUpdating: false,
				updateButtonText: 'Update'						
			});
		})
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

	toggleOpen() {
		this.setState({
			is_open: this.state.is_open == 0 ? 1 : 0
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
								<input type="checkbox" defaultChecked={this.state.is_open} onChange={this.toggleOpen} />
								{ this.state.is_open ? <span className="label label-success">Open</span> : <span className="label label-danger">Closed</span> }
							</label>
						</div>
					</li>	
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
						<button className="btn btn-sm btn-default btn-has-icon" onClick={this.updateSettings} disabled={this.state.isUpdating}>
							{ this.state.isUpdating ? 
								<i className="fa fa-spinner fa-spin"></i> : 
								<i className="fa fa-check"></i>
							}
							{ this.state.updateButtonText }
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