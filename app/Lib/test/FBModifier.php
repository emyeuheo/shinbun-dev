<?php
class FBModifier {
    static function today_birthday_data($today_list){

        $friend_list = array();
        foreach($today_list as $tbirthday){
            if (date('Y-m-d') != date('Y-m-d', strtotime($tbirthday['birthday']))){
                $tbirthday['birthday'] = date('Y-m-d'); // set today is birthday
            }
            $friend_list[] = $tbirthday;
        }
        return $friend_list;
    }	
    
    static function next_birthday_data($next_list){
        $friend_list = array();
        //    
        $this_month = date('m');
        $this_day = date('d');
        $cnt=0;
        foreach($next_list as $nbirthday){
            $month = date('m', strtotime($nbirthday['birthday']));
            $year = date('Y', strtotime($nbirthday['birthday']));
            $day = date('d', strtotime($nbirthday['birthday']));

            // Sửa các kiểu ở đây để tạo dữ liệu ảo
            // Người thứ 1 , 2 , 3 thì cho nó sinh nhật tháng này
            if($cnt==4 || $cnt==5) //$cnt==0 || $cnt==1 || $cnt==2)
            {
                $nbirthday['birthday'] = $year.'-'.$this_month.'-'.$this_day;
                $nbirthday['next_birthday'] = $year.'-'.$this_month.'-'.$this_day;
            }
            else
            if($cnt%2==0)// người  5  7 9 thì cho nó sinh nhật tháng sau
            {
                $nbirthday['birthday'] = $year.'-'.($this_month+1).'-'.$day;
                $nbirthday['next_birthday'] = $year.'-'.($this_month+1).'-'.$day;
            }
            else
            {
                $nbirthday['birthday'] = $year.'-'.($this_month+2).'-'.$day;
                $nbirthday['next_birthday'] = $year.'-'.($this_month+2).'-'.$day;
            }
            // người 11 12 thì cho nó sinh nhật hôm này
            // Tóm lại: mình chỉ sửa dữ liệu gốc của facebook trong class này
            $friend_list[] = $nbirthday;
            $cnt++;
        }
        
        return $friend_list;
    }

    static function birthday_data($fbfriend_list){
        $friend_list = array();
        //    
        $this_month = date('m');
        $this_day = date('d');
        foreach($fbfriend_list as $c =>$friend){
            $month = date('m', strtotime($friend['birthday']));
            $year = date('Y', strtotime($friend['birthday']));
            $day = date('d', strtotime($friend['birthday']));

            // Sửa các kiểu ở đây để tạo dữ liệu ảo
            // Người thứ 1, 2, 3 thì cho nó sinh nhật hôm nay
            if($c==3 || $c==6 || $c==9)// $c==0 || $c==1 || $c==2)
            {
                $friend['birthday'] = $year.'-'.$this_month.'-'.$this_day;
                $friend['next_birthday'] = $year.'-'.$this_month.'-'.$this_day;
            }
            else if($c%2==0)// những thằng sau đó (chẵn) thì cho nó sinh nhật tháng này
            {
                if($day <= $this_day) $day = $this_day + 1;
                $friend['birthday'] = $year.'-'.$this_month.'-'.$day;
                $friend['next_birthday'] = $year.'-'.$this_month.'-'.$day;
            }
            else // những thằng sau đó (lẻ) thì cho nó sinh nhật tháng sau
            {
                $friend['birthday'] = $year.'-'.($this_month+1).'-'.$day;
                $friend['next_birthday'] = $year.'-'.($this_month+1).'-'.$day;
            }
            $friend_list[] = $friend;
        }
        
        return $friend_list;
    }
}