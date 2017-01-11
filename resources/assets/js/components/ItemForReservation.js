import React from 'react';

class ItemForReservation extends React.Component
{
	constructor(props) {
		super(props);
		
		this.state = {
			deleteButtonText: 'Delete',
			isDeleting: false
		}

		this.onDelete = this.onDelete.bind(this);
	}

	onDelete(e, item) {
		e.preventDefault();

		this.setState({
			deleteButtonText: 'Deleting',
			isDeleting: true,
		})

		this.props.onDelete(item);
	}

	render()
	{
		let item = this.props.item;
		let options = [];

		if( item.options != null )
		{			
			options = item.options.map((option, index) => {
				return (
					<span className="label label-success" key={index}>{option}</span>
				)
			})
		}

		return (
			<tr key={item.id}>
				<td>{item.title}</td>
				<td>{options}</td>
				<td>
					<button 
						className="btn btn-sm btn-danger" 
						onClick={(e) => this.onDelete(e, item.id)}
						disabled={this.state.isDeleting}
					>
						{this.state.deleteButtonText}
						{ this.state.isDeleting ? <span>&nbsp; <i className="fa fa-spinner fa-spin"></i></span> : <span></span> }						
					</button>
				</td>
			</tr>
		)
	}
}

export default ItemForReservation;