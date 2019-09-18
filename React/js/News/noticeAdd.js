class CashClass{
	constructor() {
        this.newsData  = {
            title       : '',
            img_url     : '',
            keyword     : '',
            description : '',
            status      : '0',
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