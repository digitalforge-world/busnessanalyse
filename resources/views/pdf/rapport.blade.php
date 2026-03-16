<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <style>
        @page { margin: 0; }
        body { font-family: 'DejaVu Sans', sans-serif; font-size: 11px; color: #1a1a2e; margin: 0; padding: 0; }

        .header {
            background: #0F6E56;
            color: white;
            padding: 40px 50px;
        }
        .header h1 { font-size: 24px; font-weight: bold; margin: 0; margin-top: 10px; }
        .header .brand { font-size: 10px; text-transform: uppercase; letter-spacing: 2px; opacity: 0.8; }
        .header .date { font-size: 10px; margin-top: 20px; opacity: 0.7; }

        .content { padding: 40px 50px; }
        
        .section { margin-bottom: 35px; }
        .section-title {
            font-size: 14px;
            font-weight: bold;
            color: #0F6E56;
            border-bottom: 2px solid #E1F5EE;
            padding-bottom: 8px;
            margin-bottom: 20px;
            text-transform: uppercase;
        }

        .kpi-table { width: 100%; border-collapse: collapse; margin-bottom: 25px; }
        .kpi-box { background: #f8fafc; padding: 20px; border-radius: 15px; text-align: center; width: 23%; }
        .kpi-label { font-size: 9px; color: #64748b; text-transform: uppercase; margin-bottom: 5px; }
        .kpi-value { font-size: 18px; font-weight: bold; color: #0F6E56; }

        .tag { 
            display: inline-block; 
            padding: 4px 10px; 
            border-radius: 8px; 
            font-size: 9px; 
            margin-right: 5px; 
            margin-bottom: 5px; 
        }
        .tag-green { background: #E1F5EE; color: #0F6E56; }
        .tag-red { background: #FAECE7; color: #9a3412; }
        .tag-amber { background: #FEF3C7; color: #92400e; }

        .reco-item { margin-bottom: 15px; padding: 15px; border: 1px solid #f1f5f9; border-radius: 12px; }
        .reco-title { font-weight: bold; font-size: 11px; margin-bottom: 5px; color: #1e293b; }
        .reco-desc { font-size: 10px; color: #475569; line-height: 1.5; }

        .analyse-text { line-height: 1.8; color: #334155; text-align: justify; }

        .footer {
            position: fixed;
            bottom: 0;
            width: 100%;
            padding: 20px 50px;
            text-align: center;
            font-size: 9px;
            color: #94a3b8;
            border-top: 1px solid #f1f5f9;
        }
    </style>
</head>
<body>

    <div class="header">
        <div class="brand">BIA SYSTEM // GLOBAL_INTELLIGENCE_UNIT</div>
        <h1>STRATEGIC_REPORT: {{ strtoupper($company->nom) }}</h1>
        <div class="date">Généré le {{ $date }} | ID_SESSION: {{ uniqid() }}</div>
    </div>

    <div class="content">
        <div class="section">
            <div class="section-title">EXECUTIVE_SUMMARY</div>
            <table class="kpi-table">
                <tr>
                    <td class="kpi-box">
                        <div class="kpi-label">Digital Score</div>
                        <div class="kpi-value">{{ $company->score_digital }}%</div>
                    </td>
                    <td style="width: 2%"></td>
                    <td class="kpi-box">
                        <div class="kpi-label">Growth Potential</div>
                        <div class="kpi-value">{{ $company->score_croissance }}%</div>
                    </td>
                    <td style="width: 2%"></td>
                    <td class="kpi-box">
                        <div class="kpi-label">Market Sector</div>
                        <div class="kpi-value" style="font-size: 14px">{{ strtoupper($company->secteur ?? 'GENERAL') }}</div>
                    </td>
                    <td style="width: 2%"></td>
                    <td class="kpi-box">
                        <div class="kpi-label">Region</div>
                        <div class="kpi-value" style="font-size: 14px">{{ strtoupper($company->pays ?? 'GLOBAL') }}</div>
                    </td>
                </tr>
            </table>
            <p style="font-style: italic; color: #475569; line-height: 1.6; border-left: 3px solid #0F6E56; padding-left: 15px;">"{{ $company->description }}"</p>
        </div>

        <div class="section">
            <div class="section-title">DIGITAL_FOOTPRINT_ANALYSIS</div>
            @php
                $labels = ['site_web'=>'Website','facebook'=>'Facebook','instagram'=>'Instagram','linkedin'=>'LinkedIn','twitter'=>'Twitter/X','whatsapp_business'=>'WhatsApp Business','tiktok'=>'TikTok','youtube'=>'YouTube'];
                $presence = $company->presence_web ?? [];
            @endphp
            @foreach($labels as $key => $label)
                <span class="tag {{ ($presence[$key] ?? false) ? 'tag-green' : 'tag-red' }}">
                    {{ ($presence[$key] ?? false) ? 'ACTIVE ' : 'NULL ' }}{{ $label }}
                </span>
            @endforeach
        </div>

        <div class="section">
            <div class="section-title">SWOT_INSIGHTS</div>
            @foreach(($company->points_forts ?? []) as $point)
                <span class="tag tag-green">STRENGTH: {{ $point }}</span>
            @endforeach
            @foreach(($company->opportunites ?? []) as $opp)
                <span class="tag tag-amber">OPPORTUNITY: {{ $opp }}</span>
            @endforeach
        </div>

        <div class="section">
            <div class="section-title">STRATEGIC_RECOMMENDATIONS</div>
            @foreach(($analyse->recommandations ?? []) as $reco)
            <div class="reco-item">
                <div class="reco-title">[{{ strtoupper($reco['priorite']) }}] {{ strtoupper($reco['titre']) }}</div>
                <div class="reco-desc">{{ $reco['description'] }}</div>
                <div style="font-size: 8px; color: #0F6E56; margin-top: 5px; font-weight: bold;">
                    ROI_ESTIMATE : {{ $reco['roi_estime'] }} | COST_IMPACT : {{ $reco['cout_estime'] }}
                </div>
            </div>
            @endforeach
        </div>

        <div class="section">
            <div class="section-title">DEEP_AI_REASONING_OUTPUT</div>
            <div class="analyse-text">
                {!! nl2br(e($analyse->analyse_ia)) !!}
            </div>
        </div>
    </div>

    <div class="footer">
        CONFIDENTIAL_DOCUMENT • BIA SYSTEM GLOBAL • Propulsé par Deep Reasoning Unit
    </div>

</body>
</html>
