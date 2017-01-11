// ItemsForReservation.js

import React from 'react';
import ReactDOM from 'react-dom';
import axios from 'axios';
import ItemForReservation from './ItemForReservation';

class ItemsForReservation extends React.Component
{
	constructor(props) {
		super(props);
		
		this.state = {
			itemsForReservation: app.itemsForReservation,
		}

		this.onDelete = this.onDelete.bind(this);
	}

	onDelete(item) {
		let that = this;
		let url = app.admin_path + '/dashboard/outlets/' + app.outlet_id + '/items-for-reservation/' + item;

		axios({
			method: 'DELETE',
			url: url,
			headers: {
				'X-CSRF-Token': App.csrfToken
			}
		}).then(function(response) {
			console.log('success', response)
			that.setState({
				itemsForReservation: response.data.itemsForReservation
			})
		}).catch(function(error) {
			console.log('error', error.response.data)
		});
	}

	render()
	{
		let itemsForReservation = this.state.itemsForReservation.map(item => {
			return (
				<ItemForReservation item={item} key={item.id} onDelete={this.onDelete} />
			)
		})

		return (
			<table className="table table-bordered">
				<thead>
					<tr>
						<th>Title</th>
						<th>Options</th>
						<th>&nbsp;</th>
					</tr>
				</thead>
				<tbody>
					{itemsForReservation}
				</tbody>
			</table>
		)
	}
}

ReactDOM.render(
	<ItemsForReservation />,
	document.getElementById('ItemsForReservation')
)