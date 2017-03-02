import React from 'react';
import moment from 'moment';
import ProgressiveMedia from './ProgressiveMedia';

class Card extends React.Component
{
	itemDescription() {
		let item = this.props.item;

		return {
			__html: item.desc.substr(0, 300)
		}
	}

	render()
	{
		let item = this.props.item;
		let logo = 'http://placehold.it/28x28';
		let featuredImage = 'http://placehold.it/600x500';

		if( item.merchant.logo != null )
		{
			logo = app.s3_bucket_url + item.merchant.logo;
		}

		if (item.photos.length > 0 ) {
			featuredImage = app.s3_bucket_url + item.photos[0].url;
		}

		let merchant_url = '/merchants/' + item.merchant.id;
		let post_url = '/posts/' + item.slug;
		let small_image = '/images/blurred-image.jpeg';

		return (
			<div className="Card">
				<div className="Card__header">
					<div className="Flex Flex--center">
						<img src={logo} alt={item.merchant.name} title={item.merchant.name} className="img-circle Card__logo" width="30" height="30" />
						<h4 className="Card__outlet">
							<a href={merchant_url}>{item.merchant.name}</a>
						</h4>
					</div>
					<div>
						<span className="timeago">
							{moment(item.created_at).fromNow()}
						</span>
					</div>
				</div>
				<div className="Card__image">
					<a href={post_url}>
						<ProgressiveMedia featuredImage={featuredImage}></ProgressiveMedia>
					</a>
				</div>
				<div className="Card__description">
					<h3 className="Card__title"><a href={post_url}>{item.title}</a></h3>
					<div dangerouslySetInnerHTML={this.itemDescription()}></div>
				</div>
			</div>
		)
	}
}

export default Card;
