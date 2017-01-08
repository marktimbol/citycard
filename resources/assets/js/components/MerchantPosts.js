// MerchantPosts.js

import React from 'react';
import ReactDOM from 'react-dom';
import MerchantPost from './MerchantPost';

class MerchantPosts extends React.Component
{
	constructor(props)
	{
		super(props);
	}

	render()
	{
		let posts = app.posts.map(post => {
			return (
				<MerchantPost post={post} key={post.id} />
			)
		})

		return (
			<table className="table table-bordered">
				<thead>
					<tr>
						<th>Type</th>
						<th>Title</th>
						<th></th>
					</tr>
				</thead>
				<tbody>
					{posts}
				</tbody>
			</table>
		)
	}
}

ReactDOM.render(
	<MerchantPosts />,
	document.getElementById('MerchantPosts')
);