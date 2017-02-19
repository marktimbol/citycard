// OutletCard.js
import React from 'react';
import axios from 'axios';

class OutletCard extends React.Component
{
	constructor(props)
	{
		super(props);

		this.state = {
			isSubmitting: false,
			user_outlets: app.data.user_outlets,
		}

		this.handleFollow = this.handleFollow.bind(this);
		this.handleUnfollow = this.handleUnfollow.bind(this);
	}

	handleFollow(e) {
		e.preventDefault()

		this.isSubmitting();

		let that = this;
		let outlet = this.props.outlet;

		axios.post('/api/outlets/'+outlet.id+'/follows', {
			'api_token': app.api_token
		}).then(function(response) {
			console.log('success', response);
			that.setState({
				isSubmitting: false,
				user_outlets: response.data.user_outlets,
			})
		}).catch(function(error) {
			console.log('error', error)
			that.setState({
				isSubmitting: false,
			})			
		});
	}

	handleUnfollow(e) {
		e.preventDefault();
		this.isSubmitting();

		let that = this;		
		axios({
			method: 'DELETE',
			url: '/api/outlets/'+this.props.outlet.id+'/unfollow',
			data: {
				api_token: app.api_token
			}
		}).then(function(response) {
			console.log('success', response);
			that.setState({
				isSubmitting: false,
				user_outlets: response.data.user_outlets,
			})
		}).catch(function(error) {
			console.log('error', error)
			that.setState({
				isSubmitting: false,
			})			
		});
	}

	isSubmitting() {
		this.setState({
			isSubmitting: true,
		})
	}

	render()
	{
		let outlet = this.props.outlet;

		let outlet_logo = '/images/tmp/outlet-photo.jpg';
		if( outlet.merchant.logo != null ) {
			outlet_logo = app.s3_bucket_url + outlet.merchant.logo;
		}

		let followButtonClass = 'btn btn-primary btn-follow ';
		let followButtonText = 'Follow';

		let following = this.state.user_outlets.find(outlet => outlet.id === this.props.outlet.id) ? true : false
		if( following ) {
			followButtonClass += 'btn-following';
			followButtonText = 'Following';
		}

		return (
			<div className="Explore__content no-padding-bottom">
				<div className="Explore__content--outlet">
					<div className="Explore__content--outlet-profile">
						<img src={outlet_logo} alt={outlet.name} title={outlet.name} className="img-responsive img-circle" />
						<div>
							<div className="Flex">
								<h3 className="text-ellipsis">{outlet.name}</h3>
								<span className="citycard-icon icon-verified"></span>
							</div>
						</div>
					</div>
					<button className={followButtonClass}
						onClick={following ? this.handleUnfollow : this.handleFollow}
						disabled={this.state.isSubmitting}
					>
						{followButtonText}
						{ this.state.isSubmitting ? <span>&nbsp; <i className="fa fa-spinner fa-spin"></i></span> : <span></span> }
					</button>
				</div>
			</div>
		)
	}
}

export default OutletCard;