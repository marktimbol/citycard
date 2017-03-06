import React from 'react';
import ReactDOM from 'react-dom';
import Select from 'react-select';

class CreateReward extends React.Component
{
	constructor(props)
	{
		super(props);

		this.state = {
			merchant: [],
			outlets: [],
			merchant_outlets: [],
			title: 'Free Coffee',
			quantity: 10,
			required_points: 100,
			desc: 'Testing',
			photo: '',

			isFetchingOutlets: false,
			isSubmitted: false,
			submitButtonText: 'Save'
		}

		this.handleMerchantChange = this.handleMerchantChange.bind(this);
		this.handleOutletsChange = this.handleOutletsChange.bind(this);
	}

	handleMerchantChange(merchant)
	{
		console.log(merchant);

		this.setState({ merchant, isFetchingOutlets: true })

		let that = this;
		let endpoint = '/api/merchants/' + merchant.value + '/outlets';
		axios(endpoint)
			.then(function(response) {
				console.log(response);
				that.setState({
					outlets: [],
					merchant_outlets: response.data,
					isFetchingOutlets: false,
				})
			});
	}

	handleOutletsChange(outlets)
	{
		console.log(outlets);
		this.setState({ outlets })
	}	

	onSubmit(e)
	{
		e.preventDefault();

		this.setState({
			isSubmitted: true,
			submitButtonText: 'Saving'
		})

		let that = this;
		let endpoint = app.adminPath + '/dashboard/rewards';
		let photo = document.querySelector('input[type="file"]').files[0];
		let outlets = document.getElementsByName('outlets')[0].value;

		console.log('outlets', outlets);

		var data = new FormData();
		data.append('merchant_id', this.state.merchant.value);
		data.append('outlets', outlets);
		data.append('title', this.state.title);
		data.append('quantity', this.state.quantity);
		data.append('required_points', this.state.required_points);
		data.append('desc', this.state.desc);
		data.append('photo', photo);

		axios.post(endpoint, data)
			.then(function(response) {
				console.log(response);
				that.setState({
					isSubmitted: false,
					submitButtonText: 'Save'
				})
				swal({
					title: "CityCard",
					text: response.data.message,
					type: "success",
					showConfirmButton: true,
				});
			}).catch(function(error) {
				console.log(error)
				that.setState({
					isSubmitted: false,
					submitButtonText: 'Save'
				})			
			})
	}

	render()
	{
		let merchants = [];
		app.merchants.map(merchant => {
			merchants.push({
				value: merchant.id,
				label: merchant.name
			})
		});

		let merchant_outlets = [];
		this.state.merchant_outlets.map(outlet => {
			merchant_outlets.push({
				value: outlet.id,
				label: outlet.name
			})
		});

		return (
			<form method="POST" encType="multipart/form-data" onSubmit={this.onSubmit.bind(this)}>
				<div className="form-group">
					<label htmlFor="title">Title</label>
                    <input type="text" 
                    	className="form-control" 
                    	value={this.state.title} 
                    	onChange={(e) => this.setState({ title: e.target.value })} />
				</div>			
				<div className="row">
					<div className="col-md-6">
						<div className="form-group">
							<label htmlFor="merchant">Select Merchant</label>
		                    <Select
		                        name="merchant"
		                        value={this.state.merchant}
		                        options={merchants}
		                        onChange={this.handleMerchantChange} />
						</div>
					</div>

					<div className="col-md-6">
						<div className="form-group">
							<label htmlFor="outlets">Select Outlets</label>
		                    <Select
		                        name="outlets"
		                        value={this.state.outlets}
		                        options={merchant_outlets}
	                            multi={true}
	                            joinValues		                        
		                        isLoading={this.state.isFetchingOutlets}
		                        onChange={this.handleOutletsChange} />
						</div>
					</div>
				</div>

				<div className="row">
					<div className="col-md-6">
						<div className="row">
							<div className="col-md-6">
								<div className="form-group">
									<label htmlFor="title">Quantity</label>
				                    <input type="text" 
				                    	className="form-control" 
				                    	value={this.state.quantity} 
				                    	onChange={(e) => this.setState({ quantity: e.target.value })} />
			                    </div>
							</div>

							<div className="col-md-6">
								<div className="form-group">
									<label htmlFor="title">Required Points</label>
				                    <input type="text" 
				                    	className="form-control" 
				                    	value={this.state.required_points} 
				                    	onChange={(e) => this.setState({ required_points: e.target.value })} />
			                    </div>
							</div>
						</div>
					</div>
				</div>

				<div className="form-group">
					<textarea 
						rows="10" 
						className="form-control" 
						defaultValue={this.state.desc}
						onChange={(e) => this.setState({ desc: e.target.value }) }
					></textarea>
				</div>

				<div className="row">
					<div className="col-md-6">
						<div className="form-group">
							<label htmlFor="title">Photo</label>
		                    <input type="file" 
		                    	className="form-control" />
		                </div>
					</div>
				</div>

				<div className="form-group">
					<button
						type="submit"
						className="btn btn-primary"
						onClick={this.onSubmit.bind(this)}
						disabled={this.state.isSubmitted}
					>
						{ this.state.submitButtonText }
						{ this.state.isSubmitted ? <span>&nbsp; <i className="fa fa-spinner fa-spin"></i></span> : <span></span> }
					</button>					
				</div>	
			</form>
		)
	}
}

ReactDOM.render(
	<CreateReward />,
	document.getElementById('CreateReward')
);