import React from 'react';
import TimeAgo from 'react-timeago';

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

		return (
			<div className="Card">
				<div className="Card__header">
					<div className="Flex Flex--center">
						<img src={logo} alt={item.merchant.name} title={item.merchant.name} className="img-circle Card__logo" width="30" height="30" />
						<h4 className="Card__title">
							<a href="#">{item.merchant.name}</a>
						</h4>
					</div>
					<div>
						<span className="timeago">
							<TimeAgo date={item.created_at} />
						</span>
					</div>
				</div>
				<div className="Card__image">
					<img src={ featuredImage } alt={ item.title } title={ item.title } className="img-responsive" />
				</div>
				<div className="Card__description">
					<h3>{item.title}</h3>
					<div dangerouslySetInnerHTML={this.itemDescription()}></div>
				</div>
			</div>
		)
	}
}

export default Card;
