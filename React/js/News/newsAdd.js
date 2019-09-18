class CashClass{
	constructor() {
        this.newsData  = {
            title       : '',
            keyword     : '',
            description : '',
            status      : '0',
            seo_news    : '0',
            content     : ''
        };        
    }	
	
	getConfig(){
	    return {
	    	newsData : this.newsData 
	    }    	
	}
	
	
	getData(){
		return{				
			newsData : this.newsData
	    } 
	}
}
var cash = new CashClass();
export default cash;