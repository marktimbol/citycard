// MerchantPosts.js

import React from 'react';
import moment from 'moment';
import axios from 'axios';

class MerchantPost extends React.Component
{
	constructor(props)
	{
		super(props);

		this.state = {
			published: this.props.post.published,
		}

		this.handleChange = this.handleChange.bind(this);
	}

	componentDidMount() {
		let post = this.props.post;

		console.log('Post ' + post.id, this.state.published);
		console.log(moment().format('YYYY-MM-DD'));
	}

	handleChange(e) {
		let that = this;
		let selectedPost= e.target.value;
		let endpoint = app.admin_path + '/dashboard/merchants/' + app.merchant_id + '/posts/' + selectedPost + '/toggle';

		axios.put(endpoint, {
			data: {
				published: this.state.published,
			},
			headers: {
				'X-CSRF-Token': App.csrfToken,
			}			
		}).then(function(response) {
			that.setState({
				published: response.published
			})
		})
	}

	render()
	{
		let post = this.props.post;
		let url = app.admin_path + '/dashboard/merchants/' + app.merchant_id + '/posts/' + post.id;

		let eventClass = 'label label-success';
		if( moment(post.event_date).format('YYYY-MM-DD') < moment().format('YYYY-MM-DD') ) {
			eventClass = 'label label-danger';
		}

		return (
			<tr>
				<td>{post.type}</td>
				<td>
					<a href={url}>{post.title}</a>
					{ post.type == 'events' ?
						<div>
							<span className={eventClass}>
								Event Date:
								{moment(post.event_date).format('MMMM Do, YYYY')}
								&mdash; {post.event_time}
							</span>
						</div>
						: <span></span>
					}
				</td>
				<td>
					<div className="checkbox">
						<label>
							<input type="checkbox" 
								name="unpublish" 
								value={post.id} 
								defaultChecked={this.state.published}
								onChange={this.handleChange} /> Publish
						</label>
					</div>
				</td>
			</tr>
		)
	}
}

export default MerchantPost;
