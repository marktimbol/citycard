// MerchantPosts.js

import React from 'react';
import moment from 'moment';

class MerchantPost extends React.Component
{
	constructor(props)
	{
		super(props);

		this.state = {
			is_published: this.props.post.published,
		}

		this.handleChange = this.handleChange.bind(this);
	}

	componentDidMount() {
		console.log(moment().format('YYYY-MM-DD'));
	}

	handleChange(e) {
		e.preventDefault()
		console.log(e.target.value);
	}

	render()
	{
		let post = this.props.post;

		return (
			<tr>
				<td>{post.type}</td>
				<td>
					<a href="#">{post.title}</a>	
				</td>
				<td>
					<div className="checkbox">
						<label>
							<input type="checkbox" name="unpublish" value={post.id} onChange={this.handleChange} /> Publish
						</label>
					</div>
				</td>
			</tr>
		)
	}
}

export default MerchantPost;
